import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table'
import TextField from 'material-ui/TextField'

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1)
}

export default class IncomeView extends React.Component {

    render() {
        return (
            <Table selectable={false}>
                <TableBody displayRowCheckbox={false}>
                    {Object.keys(this.props.income || {}).map((key) =>
                        <TableRow key={key}>
                            <TableRowColumn style={{width:'20%'}}>{ucfirst(key)}</TableRowColumn>
                            <TableRowColumn>
                                {!this.props.editable
                                ?    <TextField
                                        disabled={true}
                                        multiLine={true}
                                        fullWidth={true}
                                        underlineShow={false}
                                        style={{cursor: 'text'}}
                                        textareaStyle={{color: 'black'}}
                                        id={this.props.income[key]}
                                        value={this.props.income[key]} />
                                :   <TextField
                                        onChange={() => window.dispatchEvent(new Event('resize'))}
                                        multiLine={true}
                                        fullWidth={true}
                                        hintText={key}
                                        id={this.props.income[key]}
                                        defaultValue={this.props.income[key]} />
                                }
                            </TableRowColumn>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}
