<html>
    <body>
        <table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" style="font-family:helvetica,Arial;padding:0px 0px">
            <tbody>
                <tr>
                <td>
                    <table width="100%" cellspacing="0px" cellpadding="0px" border="0px" align="center" style="height:50px">
                    <tbody>
                        <tr>
                        <td width="100%" style="background:#D5D6D5;" align="center">
                            <table width="100%">
                            <tbody><tr>
                                <td align="center">
                                <span style="font-size:1em;color:#fff;font-weight:200;font-family:helvetica,arial"><img src="https://huddio.img-us10.com/public//0c7f5ab463a287ef6000b392aa835456.png?r=170937347" style="width:125px"></span>
                                </td>
                            </tr>
                            </tbody></table>
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
                                            <td colspan="2">Hi {{ucwords($name)}}</td>
                                        </tr>
        
                                        <tr>
                                            <td colspan="2" align="center"><h3>{{$content}}</h3></td>
                                        </tr>
                                
                                        <tr>
                                            <td colspan="2">{{$title}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">- {{$description}}</td>
                                        </tr>
                                        <?php if (isset($sow_file)) {?>
                                        <tr>
                                            <td colspan="2">
                                            Scope Of Works File:
                                            <a href = "{{$sow_file}}" style="margin-left:10px;" rel = "">download</a>
                                            </td>
                                        </tr>
                                        <?php }?>
                                        <?php if (isset($tender_file)) {?>
                                        <tr>
                                            <td colspan="2">
                                            Tender Response Document:
                                            <a href = "{{$tender_file}}" style="margin-left:10px;" rel = "">download</a>
                                            </td>
                                        </tr>
                                        <?php }?>
                                        <?php if (isset($healthy_file)) {?>
                                        <tr>
                                            <td colspan="2">
                                            Health & Safety Document:
                                            <a href = "{{$healthy_file}}" style="margin-left:10px;" rel = "">download</a>
                                            </td>
                                        </tr>
                                        <?php }?>
                                        <?php if (isset($install_file)) {?>
                                        <tr>
                                            <td colspan="2">
                                            Installation Job Document:
                                            <a href = "{{$install_file}}" style="margin-left:10px;" rel = "">download</a>
                                            </td>
                                        </tr>
                                        <?php }?>
                                        <?php if (isset($upload_file)) {?>
                                        <tr>
                                            <td colspan="2">
                                            Upload File:
                                            <a href = "{{$upload_file}}" style="margin-left:10px;" rel = "">download</a>
                                            </td>
                                        </tr>
                                        <?php }?>
                                        <?php if (isset($img)) {?>
                                        <tr>
                                            <td colspan="2" align="center">
                                            <span><img src="{{$img}}" style="width:125px"></span>
                                            </td>
                                        </tr>
                                        <?php }?>
        
                                        <tr>
                                        <td colspan="2">&nbsp;</td>
                                        </tr>
            
                                        <tr>
                                            <td colspan="2" align="center"><a href="{{$invitationURL}}" style="background-color: #3f51b5; 
                                                border: none;
                                                color: white;
                                                padding: 15px 32px;
                                                text-align: center;
                                                text-decoration: none;
                                                display: inline-block;
                                                font-size: 16px;border-radius: 4px;">{{$btn_caption}}</a>
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
                                                <p>If you have any questions or clarification regarding your sirvez account, please feel free to contact us at support@sirvez.com or dial +44 779 244 1517. Our technical support team will assist you with anything you need.</b></p>
                                            </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        
    </body>
</html>