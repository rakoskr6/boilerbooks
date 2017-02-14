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
                if (res['error'] !== undefined) {
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
        return cookie.load(TOKEN_COOKIE) !== undefined
    }
    static set state(value) {
        if (value === null && cookie.load(TOKEN_COOKIE) !== undefined)
            cookie.remove(TOKEN_COOKIE)
        else if (value !== null && cookie.load(TOKEN_COOKIE) === undefined)
            cookie.save(TOKEN_COOKIE, value)
    }
}

export class Authenticate {

    static authenticate({username = required(), password = required()} = {}) {
        return APIFetch('POST', `/authenticate`, arguments[0])
    }

    static revoke({username = required()} = {}) {
        return APIFetch('DELETE', `/authenticate`, arguments[0])
    }
}

export class User {

    static add({username = required(), password = required(), first = required(),
        last = required(), email = required(), address = required(), city = required(),
        state = required(), zip = required()} = {}) {
        return APIFetch('POST', `/user/${username}`, arguments[0])
    }

    static remove({username = required()} = {}) {
        return APIFetch('DELETE', `/user/${username}`, arguments[0])
    }

    static update({username = required(), password, first, last, email,
        address, city, state, zip} = {}) {
        return APIFetch('PATCH', `/user/${username}`, arguments[0])
    }

    static view({username = required()} = {}) {
        return APIFetch('GET', `/user/${username}`)
    }

    // eslint-disable-next-line
    static search({} = {}) {
        return APIFetch('GET', `/users`)
    }

    static uploadCert({username = required(), file = required()}) {
        let data = new FormData()
        data.append('certificate', file)

        return fetch(`${API_PREFIX}/user/${username}/certificate`, {
            method: 'POST',
            credentials: 'include',
            body: data
        })
        .then(res => res.json());
    }

    static certificateLink({username = required()}) {
        return `${API_PREFIX}/user/${username}/certificate`;
    }
}

export class Organization {

    static add({name = required(), parent} = {}) {
        return APIFetch('POST', `/organization/${name}`, arguments[0])
    }

    static remove({name = required()} = {}) {
        return APIFetch('REMOVE', `/organization/${name}`, arguments[0])
    }

    static search() {
        // GET request can't have a body, must be passed in as query params
        return APIFetch('GET', `/organizations`)
    }
}

export class Rights {

}

export class Purchase {

}

export class Budget {

}

export class Income {

}
