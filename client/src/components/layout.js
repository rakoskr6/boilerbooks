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
        openPopover: false,
        anchorElement: null,
        logoutConfirm: false
    };
    observer = null;

    // Match the AppBar title with the document title.
    componentDidMount() {
        this.observer = new MutationObserver((mutations) => {
            console.log("Mutation")
            this.setState({ title: document.title })
        });
        this.observer.observe(document.querySelector('title'),
            { subtree: true, characterData: true });

        SessionStore.on("change", () => {
            this.setState({
                user: SessionStore.getSession().user
            })
        });
    }

    componentWillUnmount() {
        this.observer.disconnect();
    }

    goProfile = () => {
        this.props.router.replace('/me')
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
                    <Toolbar style={{ backgroundColor: lightBlue900 }}>
                        <ToolbarGroup>
                            <ToolbarTitle text="BoilerBooks" style={{ color: fullWhite }} />
                            <div style={{ width: 400 }}>
                                <Tabs tabItemContainerStyle={{height: 56}} onChange={this.tabChange} value={this.props.location.pathname}>
                                    <Tab label="Dashboard" value="/dashboard" />
                                    <Tab label="Purchases" value="/purchases" />
                                    <Tab label="Income" value="/income" />
                                    <Tab label="Budget" value="/budget" />
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
                                    <MenuItem primaryText="Profile" onTouchTap={this.goProfile} />
                                    <MenuItem primaryText="Logout" onTouchTap={this.goLogout} />
                                </Menu>
                            </Popover>
                        </ToolbarGroup>
                    </Toolbar>
                </Paper>
                <div style={{ marginTop: 56, paddingBottom: 32, width: '100%', overflowY: 'auto' }}>
                    {this.props.children}
                </div>
                <Paper rounded={false} zDepth={5} style={{ backgroundColor: lightBlue900, position: 'absolute', bottom: 0, width: '100%' }}>
                    <Subheader style={{ color: fade(fullWhite, 0.7) }}>{copyright(2017, "Aditya Vaidyam, Matt Molo, Kyle Rakos")}</Subheader>
                </Paper>
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
