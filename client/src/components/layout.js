import React from 'react'
import { withRouter } from 'react-router';
import { Toolbar, ToolbarGroup, ToolbarTitle } from 'material-ui/Toolbar'
import Subheader from 'material-ui/Subheader';
import {Tabs, Tab} from 'material-ui/Tabs';
import MenuItem from 'material-ui/MenuItem';
import Paper from 'material-ui/Paper';
import Popover from 'material-ui/Popover';
import IconButton from 'material-ui/IconButton';
import Menu from 'material-ui/Menu';
import SessionStore from '../flux/store.js'
import Avatar from 'material-ui/Avatar';
import { lightBlue900, fullWhite } from 'material-ui/styles/colors';
import { fade } from 'material-ui/utils/colorManipulator';
import Dialog from 'material-ui/Dialog';
import FlatButton from 'material-ui/FlatButton';
import UserView from '../components/userview.js'
import * as API from '../API.js'

// Quick util function to generate a copyright notice.
function copyright(startYear, authors) {
    var thisYear = new Date().getFullYear()
    var years = (thisYear > startYear ? startYear + '-' + thisYear : startYear)
    return `Copyright © ${years} ${authors}`
}

class Layout extends React.Component {
    state = {
        title: document.title,
        user: SessionStore.getSession().user,
        openProfile: false,
        openPopover: false,
        anchorElement: null,
        logoutConfirm: false,
        me: null
    };
    observer = null;

    // Match the AppBar title with the document title.
    componentDidMount() {
        SessionStore.on("change", () => {
            this.setState({
                user: SessionStore.getSession().user
            })
        });

        API.User.view({username: 'me'})
            .then(res => this.setState({me: res}))
            .catch(e => console.debug(e));

        API.Realtime.listen({endpoint:'a', handler:'b'});
    }

    componentWillUnmount() {
        this.observer.disconnect();
    }

    tabChange = (val, val2) => {
        this.props.router.replace(val)
    }

    avatarSelect = (event) => {
        this.setState({
            openPopover: true,
            anchorElement: event.currentTarget,
        });
    }

    avatarClose = () => {
        this.setState({
            openPopover: false,
        });
    };

    openProfile = () => {
        this.setState({
            openProfile: true,
        });
    };

    closeProfile = () => {
        this.setState({
            openProfile: false,
        });
    };

    goLogout = () => {
        this.setState({
            openPopover: false,
            logoutConfirm: true
        })
    }
    confirmLogout = () => {
        this.props.router.replace('/logout')
    }
    cancelLogout = () => {
        this.setState({
            openPopover: true,
            logoutConfirm: false
        })
    }

    render() {
        return (
            <div>
                <Paper rounded={false} zDepth={3} style={{ position: 'fixed', top: 0, width: '100%' }}>
                    <Toolbar style={{ backgroundColor: lightBlue900, zIndex: 2147483647 }}>
                        <ToolbarGroup>
                            <ToolbarTitle text="BoilerBooks" style={{ color: fullWhite }} />
                            <div style={{ width: 800 }}>
                                <Tabs tabItemContainerStyle={{height: 56}} onChange={this.tabChange} value={this.props.location.pathname}>
                                    <Tab label="Dashboard" value="/dashboard" />
                                    <Tab label="Purchases" value="/purchases" />
                                    <Tab label="Incomes" value="/incomes" />
                                    <Tab label="Budgets" value="/budgets" />
                                    <Tab label="Organizations" value="/organizations" />
                                    <Tab label="Rights" value="/rights" />
                                    <Tab label="Users" value="/users" />
                                </Tabs>
                            </div>
                        </ToolbarGroup>
                        <ToolbarGroup lastChild={true}>
                            <IconButton style={{ width: 96, height: 96 }} onClick={this.avatarSelect}>
                                <Avatar backgroundColor="white" color={lightBlue900}>
                                    { (this.state.user !== "" ? this.state.user.first : '?').charAt(0) }
                                </Avatar>
                            </IconButton>
                            {/* FIXME: Popover.marginRight doesn't work here for whatever reason.*/}
                            <Popover
                                open={this.state.openPopover}
                                anchorEl={this.state.anchorElement}
                                anchorOrigin={{horizontal: 'right', vertical: 'bottom'}}
                                targetOrigin={{horizontal: 'right', vertical: 'top'}}
                                style={{ marginTop:-16, marginRight:16 }}
                                onRequestClose={this.avatarClose}>
                                <Menu>
                                    <MenuItem primaryText="Profile" onTouchTap={this.openProfile} />
                                    <MenuItem primaryText="Logout" onTouchTap={this.goLogout} />
                                </Menu>
                            </Popover>
                        </ToolbarGroup>
                    </Toolbar>
                </Paper>
                <div style={{ marginTop: 56, paddingBottom: 56, width: '100%', overflowY: 'auto' }}>
                    {this.props.children}
                </div>
                <Paper rounded={false} zDepth={5} style={{ backgroundColor: lightBlue900, position: 'fixed', bottom: 0, width: '100%' }}>
                    <Subheader style={{ color: fade(fullWhite, 0.7) }}>{copyright(2017, "Aditya Vaidyam, Matt Molo, Kyle Rakos")}</Subheader>
                </Paper>
                <Dialog
                    title={`${document.title} / View`}
                    actions={[
                        <FlatButton
                            label="Close"
                            primary={true}
                            keyboardFocused={false}
                            onTouchTap={this.closeProfile} />
                    ]}
                    modal={false}
                    open={this.state.openProfile}
                    onRequestClose={this.closeProfile}
                    autoScrollBodyContent={true}>
                    <UserView user={this.state.me} />
                </Dialog>
                <Dialog
                    title="Are you sure you want to log out of BoilerBooks right now?"
                    actions={[
                        <FlatButton
                            label="Cancel"
                            secondary={true}
                            onTouchTap={this.cancelLogout} />,
                        <FlatButton
                            label="Logout"
                            primary={true}
                            keyboardFocused={true}
                            onTouchTap={this.confirmLogout} />
                    ]}
                    modal={false}
                    open={this.state.logoutConfirm}
                    onRequestClose={this.cancelLogout}>
                    If you've made some changes, make sure they're saved before you continue to log out.
                </Dialog>
            </div>
        );
    }
}

export default withRouter(Layout);
