/*
This file will contain all the BoilerBooks API endpoints.
*/

import cookie from 'react-cookie'

const TOKEN_COOKIE = "BOILERBOOKS-JWT"
const API_PREFIX = "http://devmoney.krakos.net/api"

// The APIError is thrown when an API endpoint returns an error instead of
// a result with a payload.
class APIError extends Error {
    constructor(message) {
        super(message)
        this.name = 'APIError'
        this.message = message
    }
}

// Destructured/named parameters defaulted to this function will throw an
// error if that parameter is missing. (Instead of marking optional/null).
function required() {
    throw new Error('missing parameter');
}

// Convenience method to wrap most of the Fetch API's nuances away.
function APIFetch(method, route, data) {
    const options = {
        method: method,
        credentials: 'include'
    }
    if (data !== undefined) {
        options.body = JSON.stringify(data)
    }
    return fetch(`${API_PREFIX}${route}`, options)
            .then(res => res.json())
            .then(res => {
                if (res['fatal'] !== undefined) {
                    throw new APIError('SERVER: ' + res['fatal']['message'] +
                                       '\n' + res['fatal']['trace'])
                } else if (res['error'] !== undefined) {
                    throw new APIError(res['error'])
                } else if (res['result']) {
                    return res['result']
                }
            })
}

// Wrapper around the JWT we get from the API as an API session.
// TODO: Should be gone...
export class APISession {
    static get state() {
        return cookie.load(TOKEN_COOKIE)
    }
    static set state(value) {
        if (value === null)
            cookie.remove(TOKEN_COOKIE)
        else if (value !== null)
            cookie.save(TOKEN_COOKIE, value)
    }
}

export class Authenticate {
    // eslint-disable-next-line
    static check({} = {}) {
        return APIFetch('GET', `/authenticate`, arguments[0])
    }

    static login({username = required(), password = required()} = {}) {
        return APIFetch('POST', `/authenticate`, arguments[0])
    }

    // eslint-disable-next-line
    static refresh({} = {}) {
        return APIFetch('PATCH', `/authenticate`, arguments[0])
    }

    // eslint-disable-next-line
    static revoke({} = {}) {
        return APIFetch('DELETE', `/authenticate`, arguments[0])
    }
}

export class User {
    static view({username = required()} = {}) {
        return APIFetch('GET', `/user/${username}`)
    }

    static add({username = required(), password = required(), first = required(),
        last = required(), email = required(), address = required(), city = required(),
        state = required(), zip = required()} = {}) {
        return APIFetch('POST', `/user/${username}`, arguments[0])
    }

    static update({username = required(), password, first, last, email,
        address, city, state, zip} = {}) {
        return APIFetch('PATCH', `/user/${username}`, arguments[0])
    }

    static remove({username = required()} = {}) {
        return APIFetch('DELETE', `/user/${username}`, arguments[0])
    }

    // eslint-disable-next-line
    static search({} = {}) {
        return APIFetch('GET', `/user`)
    }
}

export class Organization {

    static add({name = required(), parent} = {}) {
        return APIFetch('POST', `/organization/${name}`, arguments[0])
    }

    static remove({name = required()} = {}) {
        return APIFetch('DELETE', `/organization/${name}`, arguments[0])
    }

    // eslint-disable-next-line
    static search({} = {}) {
        // GET request can't have a body, must be passed in as query params
        return APIFetch('GET', `/organization`)
    }
}

export class Rights {
    static check({username = required(), organization = required(), budget = required(),
        year = required(), amount = required()} = {}) {
        return APIFetch('GET', `/check/${username}`)
    }

    static view({username = required()} = {}) {
        return APIFetch('GET', `/rights/${username}`)
    }

    static grant({username = required(), organization = required(), budget = required(),
        year = required(), amount = required()} = {}) {
        return APIFetch('POST', `/rights/${username}`, arguments[0])
    }

    static revoke({username = required(), organization = required(), budget = required(),
        year = required()} = {}) {
        return APIFetch('DELETE', `/rights/${username}`, arguments[0])
    }

    // eslint-disable-next-line
    static search({} = {}) {
        return APIFetch('GET', `/rights`)
    }
}

export class Purchase {
    static view({purchaseid = required()} = {}) {
        return APIFetch('GET', `/purchase/${purchaseid}`)
    }

    // eslint-disable-next-line
    static search({} = {}) { // FIXME: offset, limit
        return APIFetch('GET', `/purchase`)
    }
}

export class Budget {
    static add({organization = required(), name = required(), year = required(),
        amount = required()} = {}) {
        return APIFetch('POST', `/budget/${name}`, arguments[0])
    }

    static update({organization = required(), name = required(), year = required(),
        amount = required()} = {}) {
        return APIFetch('PATCH', `/budget/${name}`, arguments[0])
    }

    static remove({organization = required(), name = required(), year = required()} = {}) {
        return APIFetch('DELETE', `/budget/${name}`, arguments[0])
    }

    // eslint-disable-next-line
    static search({} = {}) {
        return APIFetch('GET', `/budget`)
    }
}

export class Income {
    static view({incomeid = required()} = {}) {
        return APIFetch('GET', `/income/${incomeid}`)
    }

    // eslint-disable-next-line
    static search({} = {}) {
        return APIFetch('GET', `/income`)
    }
}
