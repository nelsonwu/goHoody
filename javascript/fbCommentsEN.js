/*
 * Facebook Comments-Box email notifications v2.1
 * http://forum.developers.facebook.net/viewtopic.php?id=74644
 *
 * Copyright (c) 2011 Gil Goldshlager
 * http://facebook.com/gil.goldshlager
 * You can use this code as you want, but you must keep it free of charge!
 */

function fbCommentsEN(sendPath) {

	FB.Event.subscribe('comment.create', function(response) {

		// Site Name
		var siteName = $('meta[property="og:site_name"]').attr('content');
		if(siteName == undefined){
			siteName = $('title').html();
		}

		// Email message subject text
		var mailSubject = 'A new comment posted on your page.';
		
		// Comment Page Title
		var pageTitle = $('.fbcomments').attr('title');
		if(pageTitle == undefined){
			pageTitle = $('meta[property="og:title"]').attr('content');
			if(pageTitle == undefined){
				pageTitle = $('title').html();
				if(pageTitle == undefined){pageTitle = 'ERROR: In order to present the title, you must specify a \'title\' attribute in the fb:comments tag, or a og:title tag, or a regular HTML <pre><code>&lt;title&gt;&lt;/title&gt;</code></pre> tag.';}
			}
		}

		// Comment Page URL
		var pageURL = $('.fbcomments[href]').attr('href');
		if(pageURL == undefined){pageURL = document.location.href;}

		// Querying the latest comment on the page
		FB.api({
			method: 'fql.multiquery',
			queries: {
				comment: 'SELECT xid, object_id, post_id, fromid, time, text, id, username, reply_xid, post_fbid FROM comment WHERE object_id IN (SELECT comments_fbid FROM link_stat WHERE url ="'+ pageURL +'") ORDER BY time desc LIMIT 1',
				user: 'SELECT id, name, url, pic_square FROM profile WHERE id IN (SELECT fromid FROM #comment)'
			}
		},
			function(response) {
				comment = response[0].fql_result_set;
				user = response[1].fql_result_set;

				// Comment Date and Time
				var commentDate = new Date(comment[0].time*1000);
				var curr_date = commentDate.getDate();
				var curr_month = commentDate.getMonth();
				curr_month++;
				var curr_year = commentDate.getFullYear();
				var a_p = "";
				var curr_hour = commentDate.getHours();
				if (curr_hour < 12){a_p = "AM";}else{a_p = "PM";}if (curr_hour == 0){curr_hour = 12;}if (curr_hour > 12){curr_hour = curr_hour - 12;}
				var curr_min = commentDate.getMinutes();
				commentDate = curr_date + "." + curr_month + "." + curr_year + " at " + curr_hour + ":" + curr_min + " " + a_p;
				
				// Comment body text
				var commentText = comment[0].text;
				commentText = commentText.replace(/\n/g, '<br />');
				
				// The HTML email output
				var email_message =
					'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
					'<html xmlns="http://www.w3.org/1999/xhtml">'+
					'<head>'+
					'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'+
					'</head>'+
					'<body>'+
					''+
						'<div style="padding:10px; font-size:11px; font-family:\'lucida grande\',tahoma,verdana,arial,sans-serif;">'+
					''+
							'<h3 style="font-size: 11px; font-weight:normal; padding-bottom:10px; border-bottom:1px solid #e2e2e2; margin-bottom:10px;">'+
								'<a href="'+ user[0].url +'" style="color: #3B5998; cursor: pointer; text-decoration: none; font-weight: bold;">'+ user[0].name +'</a> posted a comment on your page.'+
							'</h3>'+
					''+
						'<table cellspacing="0" cellpadding="0" border="0">'+
							'<tr>'+
								'<td valign="top">'+
									'<a target="_blank" href="'+ user[0].url +'" style="float: left; text-decoration: none; margin-bottom: 8px; margin-right: 11px;">'+
										'<img alt="" src="'+ user[0].pic_square +'" style="height: 50px; width: 50px;">'+
									'</a>'+
								'</td>'+
								'<td valign="top">'+
									'<div style="height: auto; color: #333333 font-size: 13px;">'+
										'<a href="'+ user[0].url +'" target="_blank" style="font-size: 11px; color: #3B5998; cursor: pointer; text-decoration: none; font-weight: bold;">'+ user[0].name +'</a>'+
										'- <a target="_blank" href="'+ pageURL +'?fb_comment_id=fbc_'+ comment[0].id +'_'+ comment[0].object_id +'" style="color: #808080; text-decoration: none;">'+ commentDate +'</a>'+
									'</div>'+
									'<div style="margin-top: 5px;">'+
										'<div style="color: #1A1A1A; line-height: 14px; clear: both;">'+ commentText +'</div>'+
									'</div>'+
								'</td>'+
							'</tr>'+
						'</table>'+
					''+
							'<div style="color: #808080; padding-top:10px; border-top:1px solid #e2e2e2; margin-top:10px;">'+
								'- Page Title: <a href="'+ pageURL +'" style="color: #3B5998; cursor: pointer; text-decoration: none;">'+ pageTitle +'</a>'+
								'<br/>- Page URL: <a href="'+ pageURL +'" style="color: #3B5998; cursor: pointer; text-decoration: none;">'+ pageURL +'</a>'+
								'<br/>- Comment URL: <a href="'+ pageURL +'?fb_comment_id=fbc_'+ comment[0].id +'_'+ comment[0].object_id +'" style="color: #3B5998; cursor: pointer; text-decoration: none;">...?fb_comment_id=fbc_'+ comment[0].id +'_'+ comment[0].object_id +'</a>'+
							'</div>'+
					''+
						'</div>'+
					''+
					'</body>'+
					'</html>';
					
				// Sending the data to the PHP file that includes the mail() function
				$(document).ready(function(){
					$.post(sendPath,{ subject: mailSubject, message: email_message, url: pageURL }, "html");
				});
			}
		);
	});
}