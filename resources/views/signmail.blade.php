<html>
    <body>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="font-family:helvetica,Arial;padding:0px 0px">
		<tbody>
            <tr>
                <td>
                    <table width="100%" cellspacing="0px" cellpadding="0px" border="0px" align="center" style="height:50px">
                    <tbody>
                        <tr>
                        <td width="100%" style="padding:10px 10px;background:#D5D6D5;text-align:left;vertical-align:middle;padding:10px">
                            <table width="100%">
                                <tbody>
                                <tr>
                                    <td width="50%">
                                    <span style="font-size:1em;color:#fff;font-weight:200;font-family:helvetica,arial"><strong>&nbsp;</strong></span>
                                    </td>
                                    <td style="text-align:right">
                                    <span style="font-size:1em;color:#fff;font-weight:200;font-family:helvetica,arial"><img src="'.base_url().'logo192.png" style="width:125px"></span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        </tr>
                    </tbody>
                    </table>
                </td>
            </tr>
		
            <tr>
                <td>
                    <table cellspacing="10px" cellpadding="0px" border="0" style="width:100%;background-color:#eeeeee;font-size:12px;font-family:helvetica,arial">
                        <tr>
                            <td style="font-family:helvetica,arial;background-color:White;padding:10px;font-size:14px;color:#555;line-height:21px;padding:20px">
                                <table cellpadding="5px" border="0" style="font-size:1em;color:#555;font-size:14px;line-height:120%;margin-top:10px">
                                    <tr>
                                        <td colspan="2" align="center"><h3>Welcome to Sirvez</h3></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">Dear {{$project['first_name']}}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">Please sign off "{{$project['project_name']}}" Site Survey send by "{{$project['account_manager']}}" at "{{$project['company_name']}}"</td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                
                                    <tr>
                                        <td colspan="2">Please log in to your account dashboard to sign of the site survey follow this link: https://app.sirvez.com/ </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">
                                            <br/>User name: {{$project['email']}}
                                            <br/>Password: {{$project['password']}}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                    
                                </table>
                            </td>
                        </tr>
                        <tr style="background-color:#eeeeee;padding-bottom:30px">
                            <td>
                                <table cellspacing="0" cellpadding="0" border="0" align="center" style="text-align:center;font-size:10px;margin:15px;color:#808080">
                                    <tbody>
                                        <tr>
                                        <td>
                                            <p>If you have any questions or clarification regarding your Sirvez account, please feel free to contact us at support@sirvez.com or dial +44 779 244 1517. Our technical support team will assist you with anything you need.</b></p>
                                        </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>