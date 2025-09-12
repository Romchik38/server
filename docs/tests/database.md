# Database tests

## Integration database tests

To run integration tests use:

1. Build
    `docker build -t my-postgres .`

2. Run container
    `docker run -d --name pg-container -p 5432:5432 my-postgres`

3. Test database is ready. You can connect `psql -U testuser testdb -h localhost`. See passw in the [dockerfile](./../../Dockerfile)

4. After that you can run integration tests from `tests/Integration`

5. Next time just run `docker container start pg-container`
