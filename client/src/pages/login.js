import React from 'react';
import { withRouter } from 'react-router';
import Paper from 'material-ui/Paper';
import Snackbar from 'material-ui/Snackbar';
import { Flex, withReflex } from 'reflexbox'
import TextField from 'material-ui/TextField';
import RaisedButton from 'material-ui/RaisedButton';
import Subheader from 'material-ui/Subheader';
import * as Actions from '../flux/actions.js'
import { APISession, Authenticate, User } from "../API.js";
import { lightBlue900 } from 'material-ui/styles/colors';

const inputSubmitStyle = {
    cursor: 'pointer',
    position: 'absolute',
    top: 0,
    bottom: 0,
    right: 0,
    left: 0,
    width: '100%',
    opacity: 0,
}

const PaperFlex = withReflex()(Paper)

class Login extends React.Component {
    state = {
        username: "",
        password: "",
        errorText: ""
    }

    componentDidMount() {
        document.title = "Login"
    }

    handleChange = (event) => {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({[name]: value});

        if (this.state.errorText) {
            this.setState({errorText: ""})
        }
    }

    handleSubmit = (event) => {
        event.preventDefault();

        Authenticate.authenticate({
            username: this.state.username,
            password: this.state.password
        }).then(res => {
            APISession.state = res
            return User.view({username: 'me'})
        }).then(res => {
            console.debug(res)
            Actions.setUser(res)
            this.props.router.replace('/dashboard')
        }).catch(err => {
            console.warn("error with auth request", err)
            this.setState({
                errorText: `error: ${err.message}`
            })
        })
    }

    handleRegister = (event) => {
        this.props.router.push('/register')
    }

    render() {
        return (
            <Flex justify='space-around' align='center' style={{marginTop: '48px'}}>
                <PaperFlex flexColumn={true} justify='center' align='center' p={2} zDepth={3} col={4}>
                    <h1 style={{ marginTop: '0.67em', marginBottom: 0 }}>Please log in.</h1>
                    {/*<h3 style={{ margin: '0.0em', color: lightBlue900 }}>BoilerBooks</h3>*/}
                    <Subheader style={{ lineHeight: '0.5em', paddingLeft: 0, color: lightBlue900 }}>BoilerBooks</Subheader>
                    <form action="" onSubmit={this.handleSubmit}>
                        <TextField
                            hintText="Username"
                            floatingLabelText="Username"
                            style={{width: '100%'}}
                            name="username"
                            value={this.state.username}
                            onChange={this.handleChange}
                        />
                        <br />
                        <TextField
                            hintText="Password"
                            floatingLabelText="Password"
                            type="password"
                            style={{width: '100%'}}
                            name="password"
                            value={this.state.password}
                            onChange={this.handleChange}
                        />
                        <br /> <br />
                        <RaisedButton
                            label="Register"
                            primary={false}
                            style={{width: '45%'}}
                            onClick={this.handleRegister}>
                        </RaisedButton>
                        <RaisedButton
                            label="Login"
                            primary={true}
                            className="submit"
                            style={{float: 'right', width: '45%'}}
                            onClick={this.handleSubmit}>
                            <input type="submit" style={inputSubmitStyle}/>
                        </RaisedButton>
                    </form>
                    <Snackbar
                        open={this.state.errorText !== ""}
                        message={this.state.errorText}
                        autoHideDuration={2000}
                    />
                </PaperFlex>
            </Flex>
        );
    }
}

export default withRouter(Login);
