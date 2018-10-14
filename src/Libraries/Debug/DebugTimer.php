<?php

namespace Tmd\LaravelHelpers\Libraries\Debug;

class DebugTimer
{
    private $times = [];
    private $calls = [];
    private $running = [];

    public function startTimer($name)
    {
        if (!isset($this->calls[$name])) {
            $this->calls[$name] = 1;
        } else {
            ++$this->calls[$name];
        }

        $this->running[$name] = microtime(true);
    }

    public function stopTimer($name)
    {
        $ended = microtime(true);
        $started = $this->running[$name];
        $time = $ended - $started;
        if (isset($this->times[$name])) {
            $this->times[$name] += $time;
        } else {
            $this->times[$name] = $time;
        }
    }

    public function getAll()
    {
        return $this->times;
    }

    public function getAllInfo()
    {
        $info = [];
        foreach ($this->times as $name => $seconds) {
            $calls = $this->calls[$name];
            $avg = $seconds / $calls;
            $info[$name] = "{$seconds} seconds, {$calls} calls, {$avg} average";
        }

        return $info;
    }
}
