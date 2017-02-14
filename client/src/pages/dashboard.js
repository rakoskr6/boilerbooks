import React from 'react'
import {Card, CardTitle, CardText } from 'material-ui/Card';

export default class Dashboard extends React.Component {

    componentWillMount() {
        document.title = "Dashboard"
    }

    render() {
        return (
            <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                <CardTitle title={document.title} />
                <CardText>
                    <p>This is IEEE's new expense and income tracking system. This will be used to keep track of committee purchases. You should be able to submit expenses for approval and reimbursment. If you have the appropriate level of permissions you can also approve purchases, process donations, and view committee expenses. If you have any questions please send us an email at ieee@purdue.edu and also view the guide.</p>
                </CardText>
            </Card>
        );
    }
}
