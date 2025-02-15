<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\Events\Mappers;

use Ilch\Database\Exception;
use Modules\Events\Models\Events as EventModel;
use Modules\Events\Mappers\Events as EventMapper;

class Events extends \Ilch\Mapper
{
    /**
     * Gets the Event entries.
     *
     * @param array $where
     * @return EventModel[]|array
     */
    public function getEntries(array $where = []): ?array
    {
        $entryArray = $this->db()->select('*')
            ->from('events')
            ->where($where)
            ->order(['start' => 'ASC'])
            ->execute()
            ->fetchRows();

        if (empty($entryArray)) {
            return null;
        }

        $entry = [];
        foreach ($entryArray as $entries) {
            $entryModel = new EventModel();
            $entryModel->setId($entries['id'])
                ->setUserId($entries['user_id'])
                ->setStart($entries['start'])
                ->setEnd($entries['end'])
                ->setTitle($entries['title'])
                ->setPlace($entries['place'])
                ->setType($entries['type'])
                ->setWebsite($entries['website'])
                ->setLatLong($entries['lat_long'])
                ->setImage($entries['image'])
                ->setText($entries['text'])
                ->setCurrency($entries['currency'])
                ->setPrice($entries['price'])
                ->setPriceArt($entries['price_art'])
                ->setShow($entries['show'])
                ->setUserLimit($entries['user_limit'])
                ->setReadAccess($entries['read_access']);
            $entry[] = $entryModel;
        }

        return $entry;
    }

    /**
     * Gets event.
     *
     * @param int $id
     *
     * @return EventModel|null
     */
    public function getEventById(int $id): ?EventModel
    {
        $eventRow = $this->db()->select('*')
            ->from('events')
            ->where(['id' => $id])
            ->execute()
            ->fetchAssoc();

        if (empty($eventRow)) {
            return null;
        }

        $eventModel = new EventModel();
        $eventModel->setId($eventRow['id'])
            ->setUserId($eventRow['user_id'])
            ->setStart($eventRow['start'])
            ->setEnd($eventRow['end'])
            ->setTitle($eventRow['title'])
            ->setPlace($eventRow['place'])
            ->setType($eventRow['type'])
            ->setWebsite($eventRow['website'])
            ->setLatLong($eventRow['lat_long'])
            ->setImage($eventRow['image'])
            ->setText($eventRow['text'])
            ->setCurrency($eventRow['currency'])
            ->setPrice($eventRow['price'])
            ->setPriceArt($eventRow['price_art'])
            ->setShow($eventRow['show'])
            ->setUserLimit($eventRow['user_limit'])
            ->setReadAccess($eventRow['read_access']);

        return $eventModel;
    }

    /**
     * Get list of upcoming events.
     *
     * @param int|null $limit
     * @return EventMapper[]|array
     * @throws Exception
     */
    public function getEventListUpcoming(?int $limit = null): ?array
    {
        $eventMapper = new EventMapper();

        $select = $this->db()->select()
            ->fields('*')
            ->from('events')
            ->where([new \Ilch\Database\Mysql\Expression\Comparison('`start`', '>', 'NOW()')])
            ->order(['start' => 'ASC']);

        if ($limit) {
            $select->limit($limit);
        }

        $rows = $select->execute()
            ->fetchRows();

        if (empty($rows)) {
            return null;
        }

        $events = [];
        foreach ($rows as $row) {
            $events[] = $eventMapper->getEventById($row['id']);
        }

        return $events;
    }

    /**
     * Get list of events a user participates in.
     *
     * @param int $userId
     * @return EventMapper[]|array
     */
    public function getEventListParticipation(int $userId): ?array
    {
        $eventMapper = new EventMapper();

        $entryRow = $this->db()->select('*')
            ->from('events')
            ->where(['user_id' => $userId])
            ->execute()
            ->fetchRows();

        if (empty($entryRow)) {
            return null;
        }

        $events = [];
        foreach ($entryRow as $row) {
            $events[] = $eventMapper->getEventById($row['id']);
        }

        return $events;
    }

    /**
     * Get list of past events.
     *
     * @param int|null $limit
     * @return EventMapper[]|array
     * @throws Exception
     */
    public function getEventListPast(?int $limit = null): ?array
    {
        $eventMapper = new EventMapper();

        $select = $this->db()->select()
            ->fields('*')
            ->from('events')
            ->where([new \Ilch\Database\Mysql\Expression\Comparison('`end`', '<', 'NOW()')])
            ->order(['start' => 'DESC']);

        if ($limit) {
            $select->limit($limit);
        }

        $rows = $select->execute()
            ->fetchRows();

        if (empty($rows)) {
            return null;
        }

        $events = [];
        foreach ($rows as $row) {
            $events[] = $eventMapper->getEventById($row['id']);
        }

        return $events;
    }

    /**
     * Get a list of the current events.
     *
     * @param int|null $limit
     * @return array|null
     * @throws Exception
     */
    public function getEventListCurrent(?int $limit = null): ?array
    {
        $eventMapper = new EventMapper();

        $select = $this->db()->select()
            ->fields('*')
            ->from('events')
            ->where([
                new \Ilch\Database\Mysql\Expression\Comparison('`start`', '<', 'NOW()'),
                new \Ilch\Database\Mysql\Expression\Comparison('`end`', '>', 'NOW()'),
                ])
            ->order(['start' => 'DESC']);

        if ($limit) {
            $select->limit($limit);
        }

        $rows = $select->execute()
            ->fetchRows();

        if (empty($rows)) {
            return null;
        }

        $events = [];
        foreach ($rows as $row) {
            $events[] = $eventMapper->getEventById($row['id']);
        }

        return $events;
    }

    /**
     * Check if table exists.
     *
     * @param string $table
     * @return false|true
     * @throws Exception
     */
    public function existsTable(string $table): bool
    {
        return $this->db()->ifTableExists('[prefix]_' . $table);
    }

    /**
     * Gets the Events by start and end.
     *
     * @param string $start
     * @param string $end
     *
     * @return EventModel[]|array|null
     * @throws Exception
     */
    public function getEntriesForJson(string $start, string $end): ?array
    {
        if ($start && $end) {
            $start = new \Ilch\Date($start);
            $end = new \Ilch\Date($end);

            $entryArray = $this->db()->select()
                ->fields('*')
                ->from('events')
                ->where(['start >=' => $start, 'end <=' => $end, 'show' => 1])
                ->order(['start' => 'ASC'])
                ->execute()
                ->fetchRows();
        } else {
            return null;
        }

        if (empty($entryArray)) {
            return null;
        }

        $entry = [];
        foreach ($entryArray as $entries) {
            $entryModel = new EventModel();
            $entryModel->setId($entries['id'])
                ->setStart($entries['start'])
                ->setEnd($entries['end'])
                ->setTitle($entries['title'])
                ->setShow($entries['show'])
                ->setReadAccess($entries['read_access']);
            $entry[] = $entryModel;
        }

        return $entry;
    }

    /**
     * Get latitude and longitude for Google Maps by address
     *
     * @param string $address
     * @param string $googleMapsKey
     *
     * @return string $latlongitude
     */
    public function getLatLongFromAddress(string $address, string $googleMapsKey): ?string
    {
        $geocode = url_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . urlencode($googleMapsKey));
        $output = json_decode($geocode);

        // "OK" indicates that no errors occurred; the address was successfully parsed and at least one geocode was returned.
        if (empty($output) || $output->status !== 'OK') {
            return null;
        }

        $latitude = $output->results[0]->geometry->location->lat;
        $longitude = $output->results[0]->geometry->location->lng;
        return $latitude . ',' . $longitude;
    }

    /**
     * Returns a list of all existing types.
     *
     * @return string[]
     */
    public function getListOfTypes(): array
    {
        return $this->db()->select('type')
            ->from('events')
            ->execute()
            ->fetchList();
    }

    /**
     * Inserts or updates event model.
     *
     * @param EventModel $event
     */
    public function save(EventModel $event)
    {
        $fields = [
            'user_id' => $event->getUserId(),
            'start' => $event->getStart(),
            'end' => $event->getEnd(),
            'title' => $event->getTitle(),
            'place' => $event->getPlace(),
            'type' => $event->getType(),
            'website' => $event->getWebsite(),
            'lat_long' => $event->getLatLong(),
            'image' => $event->getImage(),
            'text' => $event->getText(),
            'currency' => $event->getCurrency(),
            'price' => $event->getPrice(),
            'price_art' => $event->getPriceArt(),
            'show' => $event->getShow(),
            'user_limit' => $event->getUserLimit(),
            'read_access' => $event->getReadAccess()
        ];

        if ($event->getId()) {
            $this->db()->update('events')
                ->values($fields)
                ->where(['id' => $event->getId()])
                ->execute();
        } else {
            $this->db()->insert('events')
                ->values($fields)
                ->execute();
        }
    }

    /**
     * Deletes event with given id.
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $imageRow = $this->db()->select('*')
            ->from('events')
            ->where(['id' => $id])
            ->execute()
            ->fetchAssoc();

        if (isset($imageRow['image']) && file_exists($imageRow['image'])) {
            unlink($imageRow['image']);
        }

        $this->db()->delete('events')
            ->where(['id' => $id])
            ->execute();

        $this->db()->delete('events_entrants')
            ->where(['event_id' => $id])
            ->execute();

        $this->db()->delete('comments')
            ->where(['key' => 'events/show/event/id/' . $id])
            ->execute();
    }

    /**
     * Delete/Unlink Image by id.
     *
     * @param int $id
     */
    public function delImageById(int $id)
    {
        $imageRow = $this->db()->select('*')
            ->from('events')
            ->where(['id' => $id])
            ->execute()
            ->fetchAssoc();

        if (file_exists($imageRow['image'])) {
            unlink($imageRow['image']);
        }

        $this->db()->update('events')
            ->values(['image' => ''])
            ->where(['id' => $id])
            ->execute();
    }
}
