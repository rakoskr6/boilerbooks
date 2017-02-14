import React from 'react'
import UserView from '../components/userview.js'
import CertUpload from '../components/certupload.js'
import {Card, CardTitle, CardText } from 'material-ui/Card';

class User extends React.Component {
    render() {
        return (
            <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                <CardTitle title={document.title} />
                <UserView user="me" />
                <CardText>Upload a new certificate:</CardText>
                <CertUpload user="me" style={{width: '100%', height: 100}}/>
            </Card>
        );
    }
}
export default User;
