# Structure

- [1]. First level - from router

                    Router
                       |
                  Root controller
                   /       \
    Next controller       No next controller (it is the last)

-------------------------------------------------------------------

- [2]. Second level - pass execution from current to next controller

    Execute if it's name        Error if not its name
                                /           \
                    if not last            it's  last
                        |                  /        \
                    Not found          no Dynamic   Dynamic
                                          |             |
                                        Not Found   Execute
                                                    /       \
                                                Not Found   Result

-------------------------------------------------------------------

- [2]. Second level - no next controller, so execute current.

                    Execute current
                  /                 \
    Default action present          No Default Action
                |                     |
    Execute Default Action          Not Found
         /           \
  Not Found         Result
