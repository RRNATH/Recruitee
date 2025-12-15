<?php
/**
 * Get Recruitee API token based on page_id
 *
 * @param array $config  Config array from config.php
 * @param string $pageId The page_id to resolve
 * @return string|null   Returns token if found, null otherwise
 */
function getRecruiteeToken(array $config, string $pageId): ?string
{
    if (isset($config['page_id_token'][$pageId])) {
        return $config['page_id_token'][$pageId];
    }

    return null;
}
