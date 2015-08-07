<?php namespace Stevenmaguire\Services\Trello\Traits;

use Stevenmaguire\Services\Trello\Exceptions\Exception;

trait BatchTrait
{
    /**
     * Batch urls
     *
     * @var array
     */
    private $batchUrls = [];

    /**
     * @return Stevenmaguire\Services\Trello\Http
     */
    abstract public function getHttp();

    /**
     * @param  array  $parameters
     *
     * @return string
     */
    abstract protected function makeQuery($parameters = []);

    /**
     * @param string $url
     *
     * @return $this
     */
    public function addBatchUrl($url)
    {
        $this->batchUrls[] = (string) $url;

        return $this;
    }

    /**
     * @param  array  $attributes
     *
     * @return object
     * @throws Exception
     */
    public function getBatch($attributes = [])
    {
        $this->parseBatchAttributes($attributes);

        try {
            $result = $this->getHttp()->get('batch' . $this->makeQuery(['urls' => $this->batchUrls]));
            $this->batchUrls = [];

            return $result;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Retrieves batch urls currently queued for request.
     *
     * @return array
     */
    public function getBatchUrls()
    {
        return $this->batchUrls;
    }

    /**
     * Attempts to parse attributes to pull valid urls.
     *
     * @param  array   $attributes
     *
     * @return void
     */
    protected function parseBatchAttributes($attributes = [])
    {
        if (isset($attributes['urls'])) {
            if (is_array($attributes['urls'])) {
                $this->addBatchUrls($attributes['urls']);
            } elseif (is_string($attributes['urls'])) {
                $this->addBatchUrl($attributes['urls']);
            }
        }
    }

    /**
     * Adds multiple urls to batch collection.
     *
     * @param array  $urls
     *
     * @return void
     */
    public function addBatchUrls(array $urls)
    {
        array_map([$this, 'addBatchUrl'], $urls);
    }
}
