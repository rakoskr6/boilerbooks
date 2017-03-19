import React from 'react'
import {Card, CardTitle, CardText } from 'material-ui/Card';
import SortableTree from 'react-sortable-tree';
import * as API from '../API.js'

export default class Dashboard extends React.Component {
    state = {
        treeData: [{ title: 'Chicken', subtitle: 'Test', children: [
                        { title: 'Egg', subtitle: 'Test' }
                  ]}],
    }

    componentWillMount() {
        document.title = "Dashboard"

        API.Organization.search().then(res => {
            API.Budget.search().then(res2 => {
                var data = new Map()
                res.forEach(thing => {
                    var buds = res2
                                .filter(val => val.organization === thing.name)
                                .map(val => {
                                    return {title: `${val.name} (${val.year})`, subtitle: `$${val.amount}`}
                                })
                    if (thing.parent == null) {
                        data.set(thing.name, {title: thing.name, subtitle: '', children: buds})
                    } else {
                        data.get(thing.parent).children.push({title: thing.name, subtitle: '', children: buds})
                    }

                })
                var list = Array.from(data.values())
                this.setState({treeData: list})
            })
        })
    }

    render() {
        return (
            <Card style={{ width: '80%', marginTop: 20, marginLeft: 'auto', marginRight: 'auto' }}>
                <CardTitle title={document.title} />
                <CardText>
                    <p>This is IEEE's new expense and income tracking system. This will be used to keep track of committee purchases. You should be able to submit expenses for approval and reimbursment. If you have the appropriate level of permissions you can also approve purchases, process donations, and view committee expenses. If you have any questions please send us an email at ieee@purdue.edu and also view the guide.</p>
                </CardText>
                <div style={{ height: 400 }}>
                    <SortableTree
                        treeData={this.state.treeData}
                        onChange={d => this.setState({treeData: d})}
                    />
                </div>
            </Card>
        );
    }
}
