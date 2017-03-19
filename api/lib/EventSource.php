<?php

/*
 * This file is part of EventSource.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class EchoHandler
{
    public function __invoke($chunk)
    {
        http_response_code(200);
        echo $chunk;
        ob_flush();
        flush();
    }
}

class Event
{
    private $comments = array();
    private $id;
    private $event;
    private $retry;
    private $data = array();

    public function addComment($comment)
    {
        $this->comments = array_merge(
            $this->comments,
            $this->extractNewlines($comment)
        );

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

   public function setRetry($retry)
    {
        if (!is_numeric($retry)) {
            throw new \InvalidArgumentException('Retry value must be numeric.');
        }

        $this->retry = $retry;

        return $this;
    }

    public function setData($data)
    {
        $this->data = $this->extractNewlines($data);

        return $this;
    }

    public function appendData($data)
    {
        $this->data = array_merge(
            $this->data,
            $this->extractNewlines($data)
        );

        return $this;
    }

    public function dump()
    {
        $response = $this->getFormattedComments().
                    $this->getFormattedId().
                    $this->getFormattedEvent().
                    $this->getFormattedRetry().
                    $this->getFormattedData();

        return '' !== $response ? $response."\n" : '';
    }

    public function getFormattedComments()
    {
        return $this->formatLines('', $this->comments);
    }

    public function getFormattedId()
    {
        return $this->formatLines('id', $this->id);
    }

    public function getFormattedEvent()
    {
        return $this->formatLines('event', $this->event);
    }

    public function getFormattedRetry()
    {
        return $this->formatLines('retry', $this->retry);
    }

    public function getFormattedData()
    {
        return $this->formatLines('data', $this->data);
    }

    private function extractNewlines($input)
    {
        return explode("\n", $input);
    }

    private function formatLines($key, $lines)
    {
        $formatted = array_map(
            function ($line) use ($key) {
                return $key.': '.$line."\n";
            },
            (array) $lines
        );

        return implode('', $formatted);
    }

    static public function create()
    {
        return new static();
    }
}

class EventWrapper
{
    private $event;
    private $source;

    public function __construct(Event $event, \Closure $source = null)
    {
        $this->event = $event;
        $this->source = $source;
    }

    public function getWrappedEvent()
    {
        return $this->event;
    }

    public function end()
    {
        if ($this->source) {
            return call_user_func($this->source);
        }
    }

    public function __call($name, $args)
    {
        if (!method_exists($this->event, $name)) {
            $message = "Could not call non-existent method '$name' on wrapped event.\n";
            $message .= 'Must be one of: '.implode(', ', get_class_methods('Igorw\EventSource\Event'));
            throw new \InvalidArgumentException($message);
        }

        $method = array($this->event, $name);
        $value = call_user_func_array($method, $args);

        if ($this->event === $value) {
            return $this;
        }

        return $value;
    }
}


/**
 * Generates a stream in the W3C EventSource format
 * http://dev.w3.org/html5/eventsource/
 */
class Stream
{
    private $buffer;
    private $handler;

    public function __construct($handler = null)
    {
        $this->buffer = new \SplQueue();
        $this->buffer->setIteratorMode(\SplQueue::IT_MODE_DELETE);
        $this->handler = $handler ?: new EchoHandler();
    }

    public function event()
    {
        $event = new Event();
        $this->buffer->enqueue($event);

        $that = $this;

        $wrapper = new EventWrapper($event, function () use ($that) {
            return $that;
        });

        return $wrapper;
    }

    public function flush()
    {
        foreach ($this->buffer as $event) {
            $chunk = $event->dump();
            if ('' !== $chunk) {
                call_user_func($this->handler, $chunk);
            }
        }
    }

    public function getHandler()
    {
        return $this->handler;
    }

    static public function getHeaders()
    {
        $origin_header = isset(getallheaders()['Origin']) ? getallheaders()['Origin'] : '*';
        error_log(json_encode(getallheaders()));
        return array(
            'Content-Type'  => 'text/event-stream',
            'Transfer-Encoding' => 'identity',
            'Cache-Control' => 'no-cache',
            'Access-Control-Allow-Origin' => $origin_header,
            'Access-Control-Allow-Credentials' => 'true'
        );
    }
}
