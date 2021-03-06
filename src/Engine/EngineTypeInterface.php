<?php

namespace Droath\ProjectX\Engine;

/**
 * Define engine type interface.
 */
interface EngineTypeInterface
{
    /**
     * Startup the engine infrastructure.
     */
    public function up();

    /**
     * Shutdown the engine infrastructure.
     */
    public function down();

    /**
     * Start the engine infrastructure.
     */
    public function start();

    /**
     * Restart the engine infrastructure.
     */
    public function restart();

    /**
     * Suspend the engine infrastructure.
     */
    public function suspend();

    /**
     * Install generic engine configurations.
     */
    public function install();
}
