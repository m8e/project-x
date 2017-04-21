<?php

namespace Droath\ProjectX\Form;

use Droath\ConsoleForm\Field\BooleanField;
use Droath\ConsoleForm\Field\SelectField;
use Droath\ConsoleForm\Field\TextField;
use Droath\ConsoleForm\Form;
use Droath\ConsoleForm\FormInterface;
use Droath\ProjectX\Project\DrupalProjectType;
use Droath\ProjectX\Project\NullProjectType;
use Droath\ProjectX\Utility;

/**
 * Define Project-X setup form.
 */
class ProjectXSetup implements FormInterface
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'project-x.form.setup';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm()
    {
        return (new Form())
            ->addFields([
                (new TextField('name', 'Project name'))
                    ->setDefault($this->getDefaultName()),
                (new SelectField('type', 'Project type'))
                    ->setOptions($this->getProjectTypes())
                    ->setDefault('drupal'),
                (new SelectField('version', 'Project version'))
                    ->setFieldCallback(function ($field, $results) {
                        $classname = $this->projectTypeClassname($results['type']);
                        $field->setOptions(
                            $classname::SUPPORTED_VERSIONS
                        )
                        ->setDefault($classname::DEFAULT_VERSION);
                    }),
                (new SelectField('engine', 'Select engine'))
                    ->setOptions($this->getEngines())
                    ->setDefault('docker'),
                (new BooleanField('github', 'Setup GitHub?'))
                    ->setSubform(function ($subform, $value) {
                        if ($value === true) {
                            $subform->addFields([
                                (new TextField('url', 'GitHub URL')),
                            ]);
                        }
                    }),
                (new BooleanField('host', 'Setup host?'))
                    ->setSubform(function ($subform, $value) {
                        if ($value === true) {
                            $subform->addFields([
                                (new TextField('name', 'Hostname'))
                                    ->setDefault($this->getDefaultHostname()),
                                (new BooleanField('open_on_startup', 'Open browser on startup?'))
                                    ->setDefault(true),
                            ]);
                        }
                    }),
            ]);
    }

    /**
     * Get project types.
     *
     * @todo This should be pulled dynamically somehow. Remove once we have
     * a discovery/plugin system in place to find a variation of project types.
     *
     * @return array
     */
    protected function getProjectTypes()
    {
        return [
            'drupal' => 'Drupal',
        ];
    }

    /**
     * Get project type classname.
     *
     * @param string $type
     *   The project type identifier.
     *
     * @return array
     */
    protected function projectTypeClassname($type)
    {
        switch ($type) {
            case 'drupal':
                return DrupalProjectType::class;

            default:
                return NullProjectType::class;
        }
    }

    /**
     * The current directory.
     *
     * @return string
     */
    protected function getCurrentDir()
    {
        return basename(getcwd());
    }

    /**
     * Get default name based on current directory.
     *
     * @return string
     */
    protected function getDefaultName()
    {
        $name = str_replace(
            ['-', '_'],
            ' ',
            $this->getCurrentDir()
        );

        return ucwords($name);
    }

    /**
     * Get default hostname based on current directory.
     *
     * @return string
     */
    protected function getDefaultHostname()
    {
        $hostname = Utility::machineName($this->getCurrentDir());

        return "local.{$hostname}.com";
    }

    /**
     * Get available engines.
     *
     * @return array
     */
    protected function getEngines()
    {
        return [
            'docker' => 'Docker',
        ];
    }
}
