<?php
class Test
{
    public function run_tests(): void
    {
        $class = get_class($this);
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if ($method == 'run_tests') continue;
            try {
                $this->$method();
                echo "<p class=\"test-good\">Test `{$class}::{$method}' superato</p>";
            } catch (Exception|Error $e) {
                $msg = $e->getMessage();
                $trace = $e->getTraceAsString();
                echo "<p class=\"test-bad\">Test `{$class}::{$method}' fallito: {$msg}<br>$trace</p>";
            }
        }
    }
}
