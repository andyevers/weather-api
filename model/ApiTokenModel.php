<?php

namespace App\Model;

class ApiTokenModel extends Database
{
    private $table_api_tokens = 'api_tokens';

    /**
     * Adds a row to the Database with the api_token provided
     */
    public function insert_api_token(string $api_token): bool
    {
        if (!$this->is_token_string($api_token)) {
            throw new \Exception('Invalid API key');
            return false;
        } else {
            $sanitized_token = preg_replace("/[^a-zA-Z0-9]+/", "", $api_token);
            $statement = $this->connection->prepare("INSERT INTO $this->table_api_tokens(Token) VALUES (?)");
            $statement->bind_param('s', $sanitized_token);

            return $statement->execute();
        }
    }

    /**
     * Returns true if value is alphanumeric string of length 64.
     */
    public function is_token_string(string $string): bool
    {
        return is_string($string) && ctype_alnum($string) && strlen($string) === 64;
    }

    /**
     * Returns true if api token exists in the database.
     */
    public function is_valid(string $api_token): bool
    {
        if (!$this->is_token_string($api_token)) return false;
        return $this->get_row($api_token) ? true : false;
    }

    /**
     * Returns a row from the database or false if no row found
     */
    public function get_row(string $api_token): array
    {
        $statement = $this->connection->prepare("SELECT 1 FROM $this->table_api_tokens WHERE Token=?");
        $statement->bind_param('s', $api_token);
        $statement->execute();
        $result = $statement->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Increments the UsageCount and sets the LastUsedOn values to current timestamp of the row with the API token provided
     */
    public function record_use(string $api_token): bool
    {
        $sql = "UPDATE $this->table_api_tokens 
                SET UsageCount = UsageCount + 1, 
                    LastUsedOn = CURRENT_TIMESTAMP 
                WHERE Token=?";

        $statement = $this->connection->prepare($sql);
        $statement->bind_param('s', $api_token);
        return $statement->execute();
    }
}
