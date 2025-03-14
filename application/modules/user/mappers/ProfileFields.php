<?php

/**
 * @copyright Ilch 2
 * @package ilch
 */

namespace Modules\User\Mappers;

use Modules\User\Models\ProfileField as ProfileFieldModel;

class ProfileFields extends \Ilch\Mapper
{
    /**
     * Returns all profile-fields.
     *
     * @param array $where
     * @return array()|\Modules\User\Models\ProfileField
     */
    public function getProfileFields(array $where = []): array
    {
        $profileFieldRows = $this->db()->select('*')
            ->from('profile_fields')
            ->where($where)
            ->order(['position' => 'ASC'])
            ->execute()
            ->fetchRows();

        $profileFields = [];
        if (!empty($profileFieldRows)) {
            foreach ($profileFieldRows as $profileFieldRow) {
                $profileField = $this->loadFromArray($profileFieldRow);
                $profileFields[] = $profileField;
            }
        }
        return $profileFields;
    }

    /**
     * Returns a ProfileField model found by the id.
     *
     * @param int $id
     * @return null|ProfileFieldModel
     */
    public function getProfileFieldById(int $id): ?ProfileFieldModel
    {
        $profileFieldRow = $this->db()->select('*')
            ->from('profile_fields')
            ->where(['id' => $id])
            ->execute()
            ->fetchRows();

        if (!empty($profileFieldRow)) {
            $profileFields = array_map([$this, 'loadFromArray'], $profileFieldRow);
            return reset($profileFields);
        }
        return null;
    }

    /**
     * Returns a ProfileField model found by the key.
     *
     * @param string $key
     * @return null|ProfileFieldModel
     */
    public function getProfileFieldIdByKey(string $key): ?ProfileFieldModel
    {
        $profileFieldRow = $this->db()->select('*')
            ->from('profile_fields')
            ->where(['key' => $key])
            ->execute()
            ->fetchRows();

        if (!empty($profileFieldRow)) {
            $profileFields = array_map([$this, 'loadFromArray'], $profileFieldRow);
            return reset($profileFields);
        }
        return null;
    }

    /**
     * Updates the position of a profile-field in the database.
     *
     * @param int $id
     * @param int $position
     *
     */
    public function updatePositionById(int $id, int $position)
    {
        $this->db()->update('profile_fields')
            ->values(['position' => $position])
            ->where(['id' => $id])
            ->execute();
    }

    /**
     * Inserts or updates a ProfileField model in the database.
     *
     * @param ProfileFieldModel $profileField
     *
     * @return int The id of the updated or inserted profile-field.
     */
    public function save(ProfileFieldModel $profileField): int
    {
        $fields = [];
        $key = $profileField->getKey();

        if (!empty($key)) {
            $fields['key'] = $profileField->getKey();
            $fields['type'] = $profileField->getType();
            $fields['icon'] = $profileField->getIcon();
            $fields['addition'] = $profileField->getAddition();
            $fields['options'] = $profileField->getOptions();
            $fields['show'] = $profileField->getShow();
            $fields['registration'] = $profileField->getRegistration();
            $fields['position'] = $profileField->getPosition();
        }

        $id = (int) $this->db()->select('id')
            ->from('profile_fields')
            ->where(['id' => $profileField->getId()])
            ->execute()
            ->fetchCell();

        if ($id) {
            // ProfileField does exist already, update.
            $this->db()->update('profile_fields')
                ->values($fields)
                ->where(['id' => $id])
                ->execute();
        } else {
            // ProfileField does not exist yet, insert.
            $id = $this->db()->insert('profile_fields')
                ->values($fields)
                ->execute();
        }

        return $id;
    }

    /**
     * Updates profile-field with given id.
     *
     * @param int $id
     */
    public function update(int $id)
    {
        $show = (int) $this->db()->select('show')
            ->from('profile_fields')
            ->where(['id' => $id])
            ->execute()
            ->fetchCell();

        if ($show == 1) {
            $this->db()->update('profile_fields')
                ->values(['show' => 0])
                ->where(['id' => $id])
                ->execute();
        } else {
            $this->db()->update('profile_fields')
                ->values(['show' => 1])
                ->where(['id' => $id])
                ->execute();
        }
    }

    /**
     * Deletes a given profile-field with the given id.
     *
     * @param int $id
     *
     * @return bool True if success, otherwise false.
     */
    public function deleteProfileField(int $id): bool
    {
        return $this->db()->delete('profile_fields')
            ->where(['id' => $id])
            ->execute();
    }

    /**
     * Returns whether a profile-field exists.
     *
     * @param int $id
     *
     * @return bool True if a profile-field with this id exists, false otherwise.
     */
    public function profileFieldWithIdExists(int $id): bool
    {
        return (boolean) $this->db()->select('COUNT(*)', 'profile_fields', ['id' => $id])
            ->execute()
            ->fetchCell();
    }

    /**
     * Returns the count of profile-fields.
     *
     *
     * @return int The count of profile-fields.
     */
    public function getCountOfProfileFields(): int
    {
        return $this->db()->select('*')
            ->from('profile_fields')
            ->execute()
            ->getNumRows();
    }

    /**
     * Returns a profile-field created using an array with data.
     *
     * @param array $profileFieldRow
     * @return ProfileFieldModel
     */
    public function loadFromArray(array $profileFieldRow = []): ProfileFieldModel
    {
        $profileField = new ProfileFieldModel();

        if (!empty($profileFieldRow['id'])) {
            $profileField->setId($profileFieldRow['id']);
        }

        if (isset($profileFieldRow['key'])) {
            $profileField->setKey($profileFieldRow['key']);
        }

        if (isset($profileFieldRow['type'])) {
            $profileField->setType($profileFieldRow['type']);
        }

        if (isset($profileFieldRow['icon'])) {
            $profileField->setIcon($profileFieldRow['icon']);
        }

        if (isset($profileFieldRow['addition'])) {
            $profileField->setAddition($profileFieldRow['addition']);
        }

        if (isset($profileFieldRow['options'])) {
            $profileField->setOptions($profileFieldRow['options']);
        }

        if (isset($profileFieldRow['show'])) {
            $profileField->setShow($profileFieldRow['show']);
        }

        if (isset($profileFieldRow['hidden'])) {
            $profileField->setHidden($profileFieldRow['hidden']);
        }

        if (isset($profileFieldRow['registration'])) {
            $profileField->setRegistration($profileFieldRow['registration']);
        }

        if (isset($profileFieldRow['position'])) {
            $profileField->setPosition($profileFieldRow['position']);
        }

        return $profileField;
    }
}
