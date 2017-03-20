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

// Take arrays of fields and query filters and form an HTTP string for
// GET requests. If neither arrays have contents, an empty string is returned.
function query_params(fields, filter) {
    var suffix = ''
    var params = []
    if (fields.length > 0) {
        params.push('fields=' + fields.join(','))
    }
    if (filter.length > 0) {
        params.push('filter=' + filter.join(','))
    }
    if (params.length > 0) {
        suffix = '?' + params.join('&')
    }
    return suffix
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
    static view({username = required(), fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/user/${username}${suffix}`)
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
    static search({fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/user${suffix}`)
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
    static search({fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/organization${suffix}`)
    }
}

export class Rights {
    static check({username = required(), organization = required(), budget = required(),
        year = required(), amount = required()} = {}) {
        return APIFetch('GET', `/check/${username}`)
    }

    static view({username = required(), fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/rights/${username}${suffix}`)
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
    static search({fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/rights${suffix}`)
    }
}

export class Purchase {
    static view({purchaseid = required(), fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/purchase/${purchaseid}${suffix}`)
    }

    // eslint-disable-next-line
    static search({fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/purchase${suffix}`)
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
    static search({fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/budget${suffix}`)
    }
}

export class Income {
    static view({incomeid = required(), fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/income/${incomeid}${suffix}`)
    }

    // eslint-disable-next-line
    static search({fields = [], filter = []} = {}) {
        var suffix = query_params(fields, filter)
        return APIFetch('GET', `/income${suffix}`)
    }
}

export class Resource {
    // eslint-disable-next-line
    static upload({} = {}) {
        // TODO: Support multipart/form-data
        return APIFetch('POST', `/resource`)
    }

    static downloadURL({id = required()} = {}) {
        return `${API_PREFIX}/resource/${id}`
    }

    static download({id = required()} = {}) {
        return APIFetch('GET', `/resource/${id}`)
    }

    static delete({id = required()} = {}) {
        return APIFetch('DELETE', `/resource/${id}`)
    }

    // eslint-disable-next-line
    static list({} = {}) {
        return APIFetch('GET', `/resource`)
    }
}

// TODO: Make this longpoll logic its own class.
var eventID = null
var listeners = []
var failures = []
export class Realtime {

    // Update the cached event ID and resume looping.
    // If an error occurs, the promise initially given is rejected.
    static eventLoop() {
        if(eventID == null)
            return

        APIFetch('GET', `/realtime/${eventID}`).then(res => {
            if (res.length > 0) {
                eventID = res[0]['id']
            }

            // Remove the id from the listeners' view and convert JSON.
            res.map(obj => {
                delete obj['id']
                obj['updates'] = JSON.parse(obj['updates'])
                return obj
            })

            // Notify all listeners and continue event loop.
            listeners.forEach(l => l(res))
            setTimeout(Realtime.eventLoop, 3000)
        }).catch(err => {
            Realtime.unlistenAll(err)
        })
    }

    static listen(handler) {
        if(typeof handler === 'function') {
            listeners.push(handler)
        }
        var p = new Promise(function(resolve, reject) {
            failures.push({resolve: resolve, reject: reject})
        })

        // If the event loop isn't happening, bootstrap it with the latest ID.
        if(eventID == null) {
            APIFetch('GET', `/realtime`).then(res => {
                console.debug(res)
                eventID = res['id']
                Realtime.eventLoop()
            })
        }
        return p
    }

    static unlistenAll(err) {
        console.error('event loop encountered error!')
        console.debug(err)

        eventID = null
        failures.forEach(l => l.reject(err))
        listeners = []
        failures = []
    }
}
