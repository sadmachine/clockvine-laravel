<?php

namespace Imarc\clockvine\Http;

use JsonSerializable;
use Illuminate\Http\Response;

class ApiResponse extends Response
{
    /**
     * Checks whether a given value is serializable.
     *
     * @param  mixed  $value
     * @return bool
     */
    static private function isSerializeable($value)
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if (!static::isSerializeable($item)) {
                    return false;
                }
            }
            return true;

        } else {
            return null === $value || is_string($value) || is_numeric($value) || is_callable([$value, '__toString'])
                || $value instanceof JsonSerizable;
        }
    }

    /**
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        parent::__construct($content, $status, $headers);

        if (!$this->headers->has('Content-Type')) {
            $this->headers->set('Content-Type', 'application/json');
        }
    }

    /**
     * Gets the current serialized response content.
     *
     * @return string Content
     *
     * @throws \UnexpectedValueException
     */
    public function getContent()
    {
        $content = $this->content;
        if (!static::isSerializeable($content)) {
            throw new \UnexpectedValueException(sprintf(
                'The Response content must be a string, implement __toString, or JsonSerializable, "%s" given.',
                gettype($content)
            ));
        }

        if (is_array($content) || $content instanceof JsonSerizable) {
            return json_encode($content);
        } else {
            return (string) $content;
        }
    }

    /**
     * Sends content for the current web response.
     *
     * @return $this
     */
    public function sendContent()
    {
        echo $this->getContent();

        return $this;
    }

    /**
     * Sets the response content.
     *
     * Valid types are strings, numbers, null, arrays, objects that implement a
     * __toString() method and objects that implement JsonSerializable.
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = ['data' => $content];

        return $this;
    }

    /**
     * Adds an error the response errors array.
     *
     * Valid types are strings, numbers, null, arrays, objects that implement a
     * __toString() method and objects that implement JsonSerializable.
     *
     * @param  mixed  $error
     *
     * @return $this
     */
    public function addError($error)
    {
        if (!isset($this->content['errors'])) {
            $this->content['errors'] = [];
        }

        $this->content['errors'][] = $error;

        return $this;
    }

    /**
     * Adds content to the meta object by a key.
     *
     * Valid types are strings, numbers, null, arrays, objects that implement a
     * __toString() method and objects that implement JsonSerializable.
     *
     * @param  string  $key
     * @param  mixed   $meta
     *
     * @return $this
     */
    public function setMeta($key, $meta)
    {
        if (!isset($this->content['meta'])) {
            $this->content['meta'] = [];
        }

        $this->content['meta'][$key] = $meta;

        return $this;
    }

    public function setSelfLink($link)
    {
        if (!isset($this->content['links'])) {
            $this->content['links'] = [];
        }

        $this->content['links']['self'] = $link;

        return $this;
    }

    public function addPaginationLinks($first = null, $last = null, $prev = null, $next = null)
    {
        if (!isset($this->content['links'])) {
            $this->content['links'] = [];
        }

        if (null !== $first) {
            $this->content['links']['first'] = $first;
        }
        if (null !== $last) {
            $this->content['links']['last'] = $last;
        }
        if (null !== $prev) {
            $this->content['links']['prev'] = $prev;
        }
        if (null !== $next) {
            $this->content['links']['next'] = $next;
        }

        return $this;
    }

    public function addInclude($content)
    {
        if (!isset($this->content['included'])) {
            $this->content['included'] = [];
        }

        $this->content['included'][] = $content;
    }
}
