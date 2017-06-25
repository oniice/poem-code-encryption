<?php

namespace Poem;

use Poem\Support\Grid;

class Encode
{
    private $indicatorLength = 5;

    /**
     * @var string[]
     */
    private $alphabet;

    public function __construct(int $indicatorLength = 5)
    {
        $this->indicatorLength = $indicatorLength;
        $this->alphabet = range('A', 'Z');
    }

    public function encode($originalPoem, $originalIndicators, $originalMessage): Grid
    {

        $poem = $this->sanitizePoem($originalPoem);

        $indicators = $this->sanitizeIndicators($originalIndicators);

        $message = $this->sanitizeMessage($originalMessage);

        $codeSequence = $this->getCodeSequence($poem, $indicators);

        $grid = $this->makeGrid(implode('', $codeSequence));

        $grid->setToGrid($message);

        return $grid;
    }

    public function decode($message, $key): Grid
    {
        $grid = $this->makeGrid($key);

        $grid->setEncodedMessageToGrid($this->sanitizeMessage($message));

        return $grid;
    }

    /**
     * Clean up the poem used for encryption
     *
     * @param $poem
     * @return string
     * @throws \Exception
     */
    private function sanitizePoem($poem): string
    {
        $poem = preg_replace('/\s+/', ' ', $poem);
        $poem = preg_replace('/[^a-z\d ]/i', '', $poem);
        $poem = preg_replace("/\r|\n/", "", $poem);

        if (!ctype_alpha(str_replace(' ', '', $poem))) {
            throw new \Exception('Poem text should only contain text, numbers found');
        }

        return strtoupper($poem);
    }

    /**
     * Clean up the chosen indicators ids
     *
     * @param $indicators
     * @return array
     * @throws \Exception
     */
    private function sanitizeIndicators($indicators): array
    {
        if (!is_array($indicators)) {
            $indicators = str_split($indicators);
        }

        $indicators = array_flip(array_flip($indicators));

        if (count($indicators) != $this->indicatorLength) {
            throw new \Exception('Indicators should contain ' . $this->indicatorLength . ' groups');
        }

        return $indicators;
    }

    /**
     * Cleanup the message chosen to encode
     *
     * @param $message
     * @return string
     * @throws \Exception
     */
    private function sanitizeMessage($message): string
    {
        $message = preg_replace('/\s+/', ' ', $message);
        $message = preg_replace('/[^a-z\d]/i', '', $message);
        $message = preg_replace("/\r|\n/", "", $message);

        if (!ctype_alpha($message)) {
            throw new \Exception('$message text should only contain text, numbers found');
        }

        return strtoupper($message);
    }

    public function getCodeSequence($poem, $indicators)
    {
        $poemChunks = $this->chunkPoem($poem);

        $sequenceChunks = $this->getSequenceChunks($poemChunks, $indicators);

        return $sequenceChunks;
    }

    private function chunkPoem($poem): array
    {
        $poemChunks = explode(' ', $poem);

        if (count($poemChunks) > count($this->alphabet)) {
            throw new \Exception('Poem text to long');
        }

        return array_combine(array_slice($this->alphabet, 0, count($poemChunks)), $poemChunks);
    }

    private function getSequenceChunks($poemChunks, $indicators)
    {
        $sequenceChunks = array_flip($indicators);
        collect($poemChunks)->only($indicators)->each(function ($item, $key) use (&$sequenceChunks) {
            $sequenceChunks[$key] = $item;
        });

        return $sequenceChunks;
    }

    /**
     * @param $completeKey
     * @return Grid
     */
    private function makeGrid($completeKey): Grid
    {
        $grid = new Grid($completeKey);
        $sequenceArray = str_split($completeKey);
        $counter = 1;
        foreach ($this->alphabet as $letter) {
            foreach ($sequenceArray as $keyIndex => $key) {
                if ($letter == $key) {
                    $grid->grid[$keyIndex][0] = [
                        'key' => $key,
                        'index' => (int)$counter,
                        'gridRef' => $keyIndex
                    ];
                    $counter++;
                }
            }
        }

        return $grid;
    }

}
