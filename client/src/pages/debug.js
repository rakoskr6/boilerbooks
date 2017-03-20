import React from 'react'
import {Card, CardTitle } from 'material-ui/Card';
import * as API from '../API.js'
import JSONTree from 'react-json-tree'

export default class Debug extends React.Component {
    state = {
        output: {}
    }

    componentWillMount() {
        document.title = "Debug"
        API.Purchase.search({
            fields: ['sum:cost', 'max:cost'],
            filter: ['organization:is:IEEE', 'year:is:2016', 'budget:is:Other']
        }).then(res1 => {
            API.Income.search({
                fields: ['sum:amount', 'max:amount'],
                filter: ['organization:is:IEEE', 'year:is:2016']
            }).then(res2 => {
                this.setState({output: {purchase: res1, income: res2}})
            })
        })
    }

    render() {
        const theme = {
            scheme: 'google',
            author: 'seth wright (http://sethawright.com)',
            base00: '#1d1f21',
            base01: '#282a2e',
            base02: '#373b41',
            base03: '#969896',
            base04: '#b4b7b4',
            base05: '#c5c8c6',
            base06: '#e0e0e0',
            base07: '#ffffff',
            base08: '#CC342B',
            base09: '#F96A38',
            base0A: '#FBA922',
            base0B: '#198844',
            base0C: '#3971ED',
            base0D: '#3971ED',
            base0E: '#A36AC7',
            base0F: '#3971ED'
        }

        return (
            <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                <CardTitle title={document.title} />
                <div style={{padding: 18}}>
                    <JSONTree data={this.state.output} theme={theme} invertTheme={true} />
                </div>
            </Card>
        );
    }
}
