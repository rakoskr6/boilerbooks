import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table';

export default class IncomeView extends React.Component {
    render() {
        return (
            <Table selectable={false}>
                <TableBody displayRowCheckbox={false}>
                    {Object.keys(this.props.income || {}).map((key) =>
                        <TableRow key={key}>
                            <TableRowColumn>{key}</TableRowColumn>
                            <TableRowColumn>{this.props.income[key]}</TableRowColumn>
                        </TableRow>
                    )}
                </TableBody>
            </Table>
        );
    }
}
