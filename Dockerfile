FROM postgres:15

ENV POSTGRES_DB=testdb \
    POSTGRES_USER=testuser \
    POSTGRES_PASSWORD=testpass

EXPOSE 5432

CMD ["postgres"]