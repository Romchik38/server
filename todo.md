# todo

## Current

  ```php
  ob_start();
  $result = pg_query_params($this->connection, $query, $params);
  $tempVar = ob_get_clean();
  ```

- controller has an uniqe id
  - [+] created id
  - [-] check unique

## Next

- @todos
- retriving path by id from root
- dynamic router takes default lang from headers  
- HEAD method
