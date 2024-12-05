# Schema

                    Controller
                        ||
                        \/
                    Moddleware
                   /          \
            Returns Data    Returns null
                  |             |
                  |             |
                  |             |
                  |          Next Middleware
                  |             |
                  |             |
                  |           Action/Next controller
                  |             |
                  |             |
                Router        Router
          
