import { EventEmitter } from "events";
import dispatcher from "./dispatcher";

class SessionStore extends EventEmitter {
    session = {
        user: ""
    }

    setUser = (user) => {
        this.session.user = user
        this.emit("SESSION_CHANGE")
    }

    getSession = () => {
        return this.session;
    }

    handleActions = (action) => {
        switch(action.type) {
            case "setUser": {
                this.setUser(action.payload);
                break;
            }
            default: {
                // Do nothing
            }
        }
    }
}

const sessionStore = new SessionStore();
dispatcher.register(sessionStore.handleActions);

export default sessionStore;
