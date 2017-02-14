import React from 'react'
import {Card, CardTitle, CardText } from 'material-ui/Card';

export default class Purchases extends React.Component {

    componentWillMount() {
        document.title = "Purchases"
    }

    render() {
        return (
            <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                <CardTitle title={document.title} />
                <CardText>
                    <p>This is where purchases go.</p>
                </CardText>
            </Card>
        );
    }
}
