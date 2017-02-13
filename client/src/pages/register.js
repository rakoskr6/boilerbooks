import React from 'react';
import { withRouter } from 'react-router';
import { Flex, withReflex } from 'reflexbox'

import Paper from 'material-ui/Paper';
import Snackbar from 'material-ui/Snackbar';
import TextField from 'material-ui/TextField';
import RaisedButton from 'material-ui/RaisedButton';

import { User, Authenticate } from "../API.js";

const PaperFlex = withReflex()(Paper)

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

class Register extends React.Component {
    state = {
        username: "",
        usernameErrText: "",
        password: "",
        passwordErrText: "",
        password2: "",
        password2ErrText: "",
        firstname: "",
        firstnameErrText: "",
        lastname: "",
        lastnameErrText: "",
        email: "",
        emailErrText: "",
        address: "",
        addressErrText: "",
        city: "",
        cityErrText: "",
        state: "",
        stateErrText: "",
        zip: "",
        zipErrText: "",
        errorText: "",
        errors: 0
    }

    componentDidMount() {
        document.title = "Register"
    }

    validator = {
        username: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }, {
            test: (val) => val.match(/^[A-Za-z0-9]+$/) !== null,
            msg: "Username must be alphanumeric"
        }],
        password: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }, {
            test: (val) => val.length >= 8,
            msg: "Password must be 8 characters or more"
        }],
        password2: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }, {
            test: (val) => val === this.state.password,
            msg: "Passwords does not match"
        }],
        email: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }, {
            test: (val) => val.match(/^.+@.+$/) !== null,
            msg: "Must be a valid email"
        }],
        state: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }, {
            test: (val) => val.match(/^[a-zA-Z]{2}$/) !== null,
            msg: "Must be 2 character state code"
        }],
        zip: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }, {
            test: (val) => val.match(/^\d{5}$/) !== null,
            msg: "Zip code must be 5 digits"
        }],
        address: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }],
        city: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }],
        firstname: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }],
        lastname: [{
            test: (val) => val !== "",
            msg: "This field is required"
        }],
    }

    handleChange = (event) => {
        const target = event.target;
        const value = target.type === 'checkbox' ? target.checked : target.value;
        const name = target.name;

        this.setState({[name]: value});

        let errored = false;

        this.validator[name].forEach(({test, msg}) => {
            if (!errored && !test(value)) {
                this.setState({[name+"ErrText"]: msg})
                errored = true;
            }
        })

        if (!errored) {
            this.setState({[name+"ErrText"]: ""})
        }

        if (this.state.errorText) {
            this.setState({errorText: ""})
        }
    }

    handleSubmit = (event) => {
        event.preventDefault();

        let errored = false;

        Object.keys(this.validator).forEach((field) => {
            let erroredField = false
            this.validator[field].forEach(({test, msg}) => {
                if (!erroredField && !test(this.state[field])) {
                    this.setState({[field+"ErrText"]: msg})
                    erroredField = true
                    errored = true;
                }
            })
        })

        if (errored) {
            this.setState({
                errorText: "Error: Still some fields to attend to"
            })
        }

        User.add({
            username: this.state.username,
            password: this.state.password,
            first: this.state.firstname,
            last: this.state.lastname,
            email: this.state.email,
            address: this.state.address,
            city: this.state.city,
            state: this.state.state,
            zip: this.state.zip
        }).then(res => {
            if (res['error'] !== undefined) {
                this.setState({
                    errorText: `Error: ${res['error']}`
                })
            }
            else if (res['result']){
                Authenticate.authenticate({
                    username: this.state.username,
                    password: this.state.password
                }).then(res => {
                    if (res['error'] !== undefined) {
                        this.props.router.replace('/login')
                    }
                    else if (res['result']){
                        this.props.router.replace('/dashboard')
                    }
                }).catch(err => {
                    this.props.router.replace('/login')
                })
            }
        })
        .catch(err => {
            console.warn("Error with auth request", err)
            this.setState({
                errorText: "Error: Please try again"
            })
        })
    }

    handleLogin = (event) => {
        this.props.router.push('/login')
    }

    render() {
        return (
            <Flex justify='space-around' align='center' style={{marginTop: '48px'}}>
                <PaperFlex flexColumn={true} justify='center' align='center' p={2} zDepth={3} col={4}>
                    <h1>Create an account.</h1>
                        <form action="" onSubmit={this.handleSubmit}>
                            <TextField
                                hintText="Mitch"
                                floatingLabelText="First Name"
                                style={{width: '100%'}}
                                name="firstname"
                                value={this.state.firstname}
                                errorText={this.state.firstnameErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="Daniels"
                                floatingLabelText="Last Name"
                                style={{width: '100%'}}
                                name="lastname"
                                value={this.state.lastname}
                                errorText={this.state.lastnameErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="president@purdue.edu"
                                floatingLabelText="Email"
                                style={{width: '100%'}}
                                name="email"
                                value={this.state.email}
                                errorText={this.state.emailErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="610 Purdue Mall"
                                floatingLabelText="Address"
                                style={{width: '100%'}}
                                name="address"
                                value={this.state.address}
                                errorText={this.state.addressErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="West Lafayette"
                                floatingLabelText="City"
                                style={{width: '100%'}}
                                name="city"
                                value={this.state.city}
                                errorText={this.state.cityErrText}
                                onChange={this.handleChange}
                                />
                            <TextField
                                hintText="IN"
                                floatingLabelText="State"
                                style={{width: '100%'}}
                                name="state"
                                value={this.state.state}
                                errorText={this.state.stateErrText}
                                onChange={this.handleChange}
                                />
                            <TextField
                                hintText="47906"
                                floatingLabelText="Zip"
                                style={{width: '100%'}}
                                name="zip"
                                value={this.state.zip}
                                errorText={this.state.zipErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="mdaniels"
                                floatingLabelText="Username"
                                style={{width: '100%'}}
                                name="username"
                                value={this.state.username}
                                errorText={this.state.usernameErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="********"
                                floatingLabelText="Password"
                                type="password"
                                style={{width: '100%'}}
                                name="password"
                                value={this.state.password}
                                errorText={this.state.passwordErrText}
                                onChange={this.handleChange}
                                />
                            <br />
                            <TextField
                                hintText="********"
                                floatingLabelText="Renter Password"
                                type="password"
                                style={{width: '100%'}}
                                name="password2"
                                value={this.state.password2}
                                errorText={this.state.password2ErrText}
                                onChange={this.handleChange}
                                />
                            <br /> <br />
                            <RaisedButton
                                label="Login"
                                primary={false}
                                style={{width: '45%'}}
                                onClick={this.handleLogin}>
                            </RaisedButton>
                            <RaisedButton
                                label="Register"
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

export default withRouter(Register);
