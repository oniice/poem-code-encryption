<?php

namespace Poem\Support;

class Grid
{
    public $key;

    public $grid;

    public $keyLength;

    public $message;

    /**
     * @var int
     */
    private $totalRows = null;

    public function __construct($sequenceArray)
    {
        $this->key = $sequenceArray;
        $this->keyLength = strlen($this->key);
    }

    /**
     * Set a message in a grid against the encryption string
     *
     * @param $message
     */
    public function setToGrid($message)
    {
        $this->message = $message;

        $keyLength = strlen($this->key);

        $messageChunks = collect(str_split($message, $keyLength))->map(function ($chunk) use ($keyLength) {
            return strtoupper(str_pad($chunk, $keyLength, 'X'));
        });

        $this->setTotalRows(count($messageChunks));

        foreach ($messageChunks as $chunk) {
            foreach (str_split($chunk) as $index => $letter) {
                $this->grid[$index][] = $letter;
            }
        }
    }

    /**
     * @param int $totalRows
     * @return Grid
     */
    private function setTotalRows(int $totalRows): Grid
    {
        $this->totalRows = $totalRows;
        return $this;
    }

    public function setEncodedMessageToGrid($message)
    {
        $this->message = $message;

        $columnLength = $this->workoutColumnLength($message);
        $blocks = str_split($message, $columnLength);

        $ordered = $this->getOrderedGrid();
        $this->setTotalRows($columnLength);

        foreach ($blocks as $index => $block) {
            foreach (str_split($block) as $letter) {
                $gridRef = $ordered[$index + 1][0]['gridRef'];
                $this->grid[$gridRef][] = $letter;
            }
        }

        return $ordered;
    }

    private function workoutColumnLength($message)
    {
        $messageChunks = $this->makeRowChunks($message);

        return count($messageChunks);
    }

    private function makeRowChunks($message)
    {
        return collect(str_split($message, $this->keyLength))->map(function ($chunk) {
            return strtoupper(str_pad($chunk, $this->keyLength, 'X'));
        });
    }

    private function getOrderedGrid()
    {
        $ordered = [];
        foreach ($this->grid as $index => $column) {
            $ordered[$column[0]['index']] = $column;
        }
        asort($ordered);

        return $ordered;
    }

    public function getOriginalMessage()
    {
        $rows = $this->getTotalRows();
        $chunks = [];

        $ordered = $this->grid;
        ksort($ordered);
        for ($x = 0; $x < $rows; $x++) {
            $chunks[$x] = '';
            foreach ($ordered as $column) {
                $chunks[$x] .= $column[$x + 1];
            }
        }

        return implode('', $chunks);
    }

    /**
     * Number of rows
     *
     * @return int
     */
    public function getTotalRows(): int
    {
        return $this->totalRows;
    }

    public function getEncodedMessage()
    {
        $ordered = $this->getOrderedGrid();
        $chunks = [];
        foreach ($ordered as $column) {
            $block = array_slice($column, 1);
            $chunks[] = collect($block)->reduce(function ($carry, $item) {
                return ($carry .= $item);
            });
        }

        return implode(' ', $chunks);
    }

    public function getKey()
    {
        return $this->key;
    }
}
