import React from 'react';
import ReactDOM from 'react-dom';
import { Router, Route, browserHistory, IndexRedirect } from 'react-router';
import './index.css';
import Layout from './components/layout.js';
import User from './pages/user.js';
import UserView from './components/userview.js';
import Login from './pages/login.js';
import Register from './pages/register.js';
import Dashboard from './pages/dashboard.js';
import { APISession } from './API.js';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {lightBlue900} from 'material-ui/styles/colors';

// This is the UI theme for the entire app.
const theme = getMuiTheme({
    palette: {
        primary1Color: lightBlue900,
    }, appBar: {
        height: 48,
    }, ripple: {
        color: lightBlue900,
    },
});

// The Index controls all authentication + routing to ensure pages work.
class Index extends React.Component {
    state = {}

    // Entry assistant: withoutAuthorization
    withoutAuthorization = (nextState, replace) => {
        if (APISession.state)
            replace('/dashboard')
    }

    // Entry assistant: requireAuthorization
    requireAuthorization = (nextState, replace) => {
        if (!APISession.state)
            replace('/login')
    }

    // Entry assistant: destroyAuthorization
    destroyAuthorization = (nextState, replace) => {
        APISession.state = null
        replace('/')
    }

    render() {
        return (
            <MuiThemeProvider muiTheme={theme}>
                <Router history={browserHistory}>

                    {/* The following routes are those that do not require
                        authorization; i.e. entry points to the App. */}
                    <Route path="/" onEnter={this.withoutAuthorization}>
                        <IndexRedirect to="/login" />
                        <Route path="/login" component={Login} />
                        <Route path="/register" component={Register} />
                    </Route>

                    {/* The following routes are those that MUST and DO require
                        authorization; i.e. actual content within the App. */}
                    <Route component={Layout} onEnter={this.requireAuthorization}>
                        <Route path="/logout" onEnter={this.destroyAuthorization} />

                        <Route path="/dashboard" component={Dashboard} />
                        <Route path="/purchases" component={Dashboard} />
                        <Route path="/income" component={Dashboard} />
                        <Route path="/budget" component={Dashboard} />

                        <Route path="/me" component={User} />
                        <Route path="/user/:user" component={UserView} />
                    </Route>
                </Router>
            </MuiThemeProvider>
        )
    }
}

// Required FIX for MaterialUI.
import injectTapEventPlugin from 'react-tap-event-plugin';
injectTapEventPlugin();

// This mounts our Index to the root div in the HTML.
ReactDOM.render(<Index />, document.getElementById('root'));
