<?php

declare(strict_types=1);

namespace CloudForest\ApiClientPhp\Modules;

use CloudForest\ApiClientPhp\Dto\ListingDto;
use GuzzleHttp\Exception\GuzzleException;

/**
 * The client for the CloudForest Listing API module.
 *
 * @package CloudForest
 */
class ListingModule extends ApiModuleBase
{
    public function __construct($id, $secret, $host, $api)
    {
        parent::__construct($id, $secret, $host, $api);
    }

    /**
     * Create a listing via the CloudForest API with the specified title
     *
     * This takes care of looking up the user information required to create a
     * listing.
     *
     * @todo Expose more properties such as description, latitidue and
     * longitude. This can be done in July 2024.
     *
     * @param  ListingDto $listing The new listing to send to CloudForest
     * @return string The UUID of the resulting listing
     * @throws GuzzleException
     */
    public function create(ListingDto $listing)
    {
        try {
            $response = $this->client->request(
                'GET',
                '/api/current-user',
                [
                    'headers' => $this->getHeadersWithAccessBearer(),
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);
        $data = $this->getDataAsArray($content);

        if (is_string($data['id'])) {
            $userId = $data['id'];
        } else {
            throw new \Exception('Listing->create: did not load a valid user id with which to create a listing');
        }

        $included = $this->getIncluded($content);
        $employments = $included['employments'];
        $companyId = '';
        if (is_array($employments) && count($employments) > 0) {
            $company = $employments[0];
            if (is_array($company) && array_key_exists('companyId', $company)) {
                $companyId = $company['companyId'];
            }
        }

        try {
            $listing->userId = $userId;
            $listing->companyId = $companyId;

            // Convert to assoc array
            $encoded = json_encode($listing);
            if (!$encoded) {
                throw new \Exception('Listing->create: Failed to encode ListingDto');
            }
            $assoc = json_decode($encoded, true);

            // Post it
            $response = $this->client->request(
                'POST',
                '/api/listings/',
                [
                    'json' => $assoc,
                    'headers' => $this->getHeadersWithAccessBearer(),
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);
        $data = $this->getDataAsString($content);
        return $data;
    }

    /**
     * Find one listing using its UUID.
     * @param string $listingUuid
     * @return mixed The listing data as an associative array.
     */
    public function findOne(string $listingUuid)
    {
        try {
            $response = $this->client->request(
                'GET',
                '/api/listings/' . $listingUuid,
                [
                    'headers' => $this->getHeadersWithAccessBearer(),
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);
        $data = $this->getDataAsArray($content);
        return $data;
    }

    /**
     * Find all the listings belonging to the user associated with the JWT.
     * @return mixed The listing data as an associative array.
     */
    public function findOwn()
    {
        try {
            $response = $this->client->request(
                'GET',
                '/api/listings/own',
                [
                    'headers' => $this->getHeadersWithAccessBearer(),
                ]
            );
        } catch (GuzzleException $e) {
            throw $e;
        }

        $body = $response->getBody()->getContents();
        $content = json_decode($body, true);
        $data = $this->getDataAsArray($content);
        return $data;
    }
}
