# Structure

                      Router
                        ||
                        \/
                    Controller
                        ||
                        \/
                Request Middleware
                   /          \
            Returns Data    Returns null
                  |             |
                  |             |
                  |             |
                  |          Next Middleware
                  |             |
                  |             |
                  |           Action/Next controller
                Router          |
                                |
                        Response Middleware
                                ||
                                \/
                              Router
