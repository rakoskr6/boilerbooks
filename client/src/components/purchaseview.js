import React from 'react'
import {Table, TableBody, TableRow, TableRowColumn} from 'material-ui/Table'
import TextField from 'material-ui/TextField'
import CertUpload from '../components/certupload.js'
import * as API from '../API.js'
import FlatButton from 'material-ui/FlatButton';
import Popover from 'material-ui/Popover'

function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1)
}

export default class PurchaseView extends React.Component {
    state = {
        receiptURL: '',
        openReceipt: null
    }

    componentDidMount() {
        var url = API.Resource.downloadURL({id: this.props.purchase['receipt']});
        this.setState({receiptURL: url});
    }

    render() {
        return (
            <div>
                <Table selectable={false}>
                    <TableBody displayRowCheckbox={false}>
                        {Object.keys(this.props.purchase || {}).map((key) =>
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
                                            id={this.props.purchase[key]}
                                            value={this.props.purchase[key]} />
                                    :   <TextField
                                            onChange={() => window.dispatchEvent(new Event('resize'))}
                                            multiLine={true}
                                            fullWidth={true}
                                            hintText={key}
                                            id={this.props.purchase[key]}
                                            defaultValue={this.props.purchase[key]} />
                                    }
                                </TableRowColumn>
                            </TableRow>
                        )}
                        <TableRow key="receipt">
                            <TableRowColumn>Receipt</TableRowColumn>
                            <TableRowColumn>
                                <FlatButton
                                    label="View"
                                    primary={false}
                                    keyboardFocused={false}
                                    onTouchTap={(event) => this.setState({openReceipt: event.currentTarget})} />
                            </TableRowColumn>
                        </TableRow>
                    </TableBody>
                </Table>
                {this.props.editable
                    ? <CertUpload user={this.props.purchase} style={{width: '100%', height: 96}} />
                    : <div />
                }
                <Popover
                  open={this.state.openReceipt != null}
                  anchorEl={this.state.openReceipt}
                  anchorOrigin={{horizontal: 'middle', vertical: 'center'}}
                  targetOrigin={{horizontal: 'middle', vertical: 'center'}}
                  onRequestClose={(event) => this.setState({openReceipt: null})}>
                    <div>
                        <a href={this.state.receiptURL} target='_blank'>
                            <img style={{width: 600, height: 'auto'}}
                                 src={this.state.receiptURL} alt="Receipt" />
                        </a>
                    </div>
                </Popover>
            </div>
        );
    }
}
