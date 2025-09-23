# Structure

                     Request
                        |
                        v
                      Router
                        |
                        v
                    Controller
                        |
                        v
                Request Middleware
                   /          \
            Returns Data    Returns null
                  |             |
                  |             |
                  v             v
                  |          Next Middleware
                  |             |
                  |             v
                  |           Action/Next controller
                  |             |
                  |             v
                  |     Response Middleware
                  |             |
                  |             |
                   \           /
                    \ _______ /
                         |
                         v
                       Router
                         |
                         v
                      Response
