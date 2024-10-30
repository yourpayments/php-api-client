<?php

declare(strict_types=1);

namespace Ypmn;

/*
 * API-USRMNG
 */
class ApiCpanelUserRequest extends ApiRequest
{
    /** ApiCpanelUser endpoints. Task YP-980 */
    protected const CPANEL_PRIVILEGE_API = '/api/v4/users/privilege';
    protected const CPANEL_ROLE_API = '/api/v4/users/role';
    protected const CPANEL_USER_API = '/api/v4/users/user';

    /**
     * @return array
     */
    public function getPrivilegesList(): array
    {
        try {
            $result = parent::sendGetRequest(self::CPANEL_PRIVILEGE_API);
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }

    /**
     * @param array $merchants
     * @return array
     */
    public function getRolesList(array $merchants = []): array
    {
        $apiUrlWithQuery = self::CPANEL_ROLE_API;
        if (!empty($merchants)) {
            $apiUrlWithQuery .= '?merchants=' . implode(',', $merchants);
        }
        try {
            $result = parent::sendGetRequest($apiUrlWithQuery);
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }

    /**
     * @param array $merchants
     * @return array
     */
    public function getUsersList(array $merchants = []): array
    {
        $apiUrlWithQuery = self::CPANEL_USER_API;
        if (!empty($merchants)) {
            $apiUrlWithQuery .= '?merchants=' . implode(',', $merchants);
        }
        try {
            $result = parent::sendGetRequest($apiUrlWithQuery);
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }

    /**
     * @param CpanelUser $newUser
     * @return array
     */
    public function createUser(CpanelUser $newUser): array
    {
        $newUserSerialized = json_encode($newUser->arraySerialize());
        try {
            $result = parent::sendPostRequest(
                $newUserSerialized,
                self::CPANEL_USER_API,
                'Ошибка создания пользователя'
            );
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }

    /**
     * @param CpanelUser $editUser
     * @return array
     */
    public function updateUser(CpanelUser $editUser): array
    {
        $editUserSerialized = json_encode($editUser->arraySerialize());
        try {
            $result = parent::sendPutRequest(
                $editUserSerialized,
                self::CPANEL_USER_API,
                'Ошибка изменения пользователя'
            );
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }

    /**
     * @param Role $newRole
     * @return array
     */
    public function createRole(Role $newRole): array
    {
        $newRoleSerialized = json_encode($newRole->arraySerialize());
        try {
            $result = parent::sendPostRequest(
                $newRoleSerialized,
                self::CPANEL_ROLE_API,
                'Ошибка создания роли'
            );
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }

    /**
     * @param Role $editRole
     * @return array
     */
    public function updateRole(Role $editRole): array
    {
        $editRoleSerialized = json_encode($editRole->arraySerialize());
        try {
            $result = parent::sendPutRequest(
                $editRoleSerialized,
                self::CPANEL_ROLE_API,
                'Ошибка изменения роли'
            );
        } catch (PaymentException $pe) {
            return ['response' => null, 'error' => $pe->getMessage()];
        }

        return $result;
    }
}
