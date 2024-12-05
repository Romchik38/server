# Structure

1. First level
                    Router
                       |
                  controller
                   /       \
    Next controller       No next controller (it is the last)


2. Execute if it's name        Error if not its name
                                /           \
                    if not last            it's  last
                        |                  /        \
                    Not found          no Dynamic   Dynamic
                                          |             |
                                        not Found   Execute
                                                    /       \
                                                Not Found   Result
