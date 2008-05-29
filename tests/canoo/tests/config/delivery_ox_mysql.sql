INSERT INTO ox_acls VALUES (1,'and','Site:Channel','=~','7',0);

INSERT INTO ox_ad_zone_assoc VALUES (1,0,1,1,0,1670960,1),(2,1,1,0.9,1,100,1),(3,0,2,0,0,1,1),(4,1,2,0,1,1,1),(5,2,1,0.9,1,100,1),(6,0,3,0,0,0,1),(7,1,3,0,1,1,1);

INSERT INTO ox_affiliates VALUES (2,1,'Agency Publisher 1','','','Andrew Hill','andrew.hill@openads.org','http://fornax.net','2007-05-15 13:41:40',NULL,'',NULL,NULL,NULL,6);

INSERT INTO ox_affiliates_extra VALUES (2,'','','','','','','','','','Cheque by post','GBP',0,0,0,NULL,NULL);


insert into ox_banners (bannerid, campaignid, contenttype, pluginversion, storagetype, filename, imageurl, htmltemplate, htmlcache, width, height, weight, seq, target, url, alt, statustext, bannertext, description, autohtml, adserver, block, capping, session_capping, compiledlimitation, acl_plugins, append, appendtype, bannertype, alt_filename, alt_imageurl, alt_contenttype, comments, updated, acls_updated, keyword, transparent, parameters, an_banner_id, as_banner_id, status, ad_direct_status, ad_direct_rejection_reason_id) values('1','1','html','0','html','','','Test HTML Banner!\r\n','Test HTML Banner!\r\n','468','60','1','0','','','','','','Test HTML Banner!','t','','0','0','0','(MAX_checkSite_Channel(\\\'7\\\', \\\'=~\\\'))','Site:Channel','','0','0','','','gif','','2008-04-28 11:20:31','2008-04-28 11:20:31','','0','N;',NULL,NULL,'0','0','0');

insert into ox_banners (bannerid, campaignid, contenttype, pluginversion, storagetype, filename, imageurl, htmltemplate, htmlcache, width, height, weight, seq, target, url, alt, statustext, bannertext, description, autohtml, adserver, block, capping, session_capping, compiledlimitation, acl_plugins, append, appendtype, bannertype, alt_filename, alt_imageurl, alt_contenttype, comments, updated, acls_updated, keyword, transparent, parameters, an_banner_id, as_banner_id, status, ad_direct_status, ad_direct_rejection_reason_id) values('2','2','html','0','html','','','html test banner','<a href=\"{clickurl}\" target=\"{target}\">html test banner</a>','468','60','1','0','','https://developer.openx.org/','','','','test banner','t','max','0','0','0','',NULL,'','0','0','','','gif','','2008-04-28 11:53:30','0000-00-00 00:00:00','','0','N;',NULL,NULL,'0','0','0');

insert into ox_banners (bannerid, campaignid, contenttype, pluginversion, storagetype, filename, imageurl, htmltemplate, htmlcache, width, height, weight, seq, target, url, alt, statustext, bannertext, description, autohtml, adserver, block, capping, session_capping, compiledlimitation, acl_plugins, append, appendtype, bannertype, alt_filename, alt_imageurl, alt_contenttype, comments, updated, acls_updated, keyword, transparent, parameters, an_banner_id, as_banner_id, status, ad_direct_status, ad_direct_rejection_reason_id) values('3','3','gif','0','sql','468x60.gif','','','','468','60','1','0','','https://developer.openx.org/','alt text','','','sample gif banner','f','','0','0','0','',NULL,'','0','0','','','gif','','2008-04-28 12:04:40','0000-00-00 00:00:00','','0','N;',NULL,NULL,'0','0','0');

INSERT INTO ox_campaigns VALUES (1,'Advertiser 1 - Default Campaign',1,100000000,-1,-1,'0000-00-00','0000-00-00',0,0,0,0,0,'f',0,'',NULL,NULL,'0000-00-00 00:00:00',0,0,0,NULL,NULL,0,0,0,0,0),(2,'test campaign',1,-1,-1,-1,'0000-00-00','0000-00-00',-1,1,0,0,0,'t',0,'',NULL,NULL,'0000-00-00 00:00:00',0,0,0,NULL,NULL,0,0,0,0,0),(3,'campaign 2 (gif)',1,-1,-1,-1,'0000-00-00','0000-00-00',0,1,0,0,0,'t',0,'',NULL,NULL,'0000-00-00 00:00:00',0,0,0,NULL,NULL,0,0,0,0,0);

INSERT INTO ox_campaigns_trackers VALUES (1,3,1,3,3,4);

INSERT INTO ox_channel VALUES (7,2,0,'Test Admin Channel 2','','true','true',1,'','0000-00-00 00:00:00','0000-00-00 00:00:00');

INSERT INTO ox_clients VALUES (1,2,'Advertiser 1','advertiser','example@example.com','f',7,'2007-04-27','t','','2007-05-16 12:54:09',2,NULL,NULL,4,0);

INSERT INTO ox_data_raw_ad_click VALUES ('1d0b8f22878ee21edac4d01eeb8793bd','','2007-08-29 15:19:19',2,0,0,NULL,NULL,'','127.0.0.1','127.0.0.1',NULL,NULL,NULL,NULL,NULL,NULL,'','Mozilla/4.0 (compatible; MSIE 6.0b; Windows 98)','','',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);


INSERT INTO ox_data_raw_ad_impression VALUES ('__7bf7b383f5a3bb57540c5fa17926ae','','2008-04-07 14:14:49',2,0,0,NULL,NULL,'en-us,en;q=0.5','127.0.0.2','127.0.0.2',NULL,NULL,NULL,NULL,NULL,NULL,'','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.13) Gecko/20080328 Fedora/1.1.9-1.fc8 SeaMonkey/1.1.9','','',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

INSERT INTO ox_data_raw_tracker_impression VALUES (1,'singleDB','6e8928c9063f85e75c8a457b42f50257','','2007-06-01 15:13:26',1,'','','en-us,en;q=0.5','127.0.0.1','127.0.0.1','',0,'','','','','','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11','','',0,'','','','0.0000','0.0000','','','','',''),(2,'singleDB','6e8928c9063f85e75c8a457b42f50257','','2007-06-01 15:13:37',1,'','','en-us,en;q=0.5','127.0.0.1','127.0.0.1','',0,'','','','','','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11','','',0,'','','','0.0000','0.0000','','','','',''),(3,'singleDB','6e8928c9063f85e75c8a457b42f50257','','2007-06-01 15:23:06',1,'','','en-us,en;q=0.5','127.0.0.1','127.0.0.1','',0,'','','','','','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11','','',0,'','','','0.0000','0.0000','','','','',''),(4,'singleDB','6e8928c9063f85e75c8a457b42f50257','','2007-06-01 15:23:07',1,'','','en-us,en;q=0.5','127.0.0.1','127.0.0.1','',0,'','','','','','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11','','',0,'','','','0.0000','0.0000','','','','',''),(5,'singleDB','6e8928c9063f85e75c8a457b42f50257','','2007-06-01 15:24:37',1,'','','en-us,en;q=0.5','127.0.0.1','127.0.0.1','',0,'','','','','','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11','','',0,'','','','0.0000','0.0000','','','','',''),(6,'singleDB','6e8928c9063f85e75c8a457b42f50257','','2007-06-01 15:25:53',1,'','','en-us,en;q=0.5','127.0.0.1','127.0.0.1','',0,'','','','','','Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.11) Gecko/20070312 Firefox/1.5.0.11','','',0,'','','','0.0000','0.0000','','','','','');

INSERT INTO ox_data_raw_tracker_variable_value VALUES (1,'singleDB',1,'2007-06-01 15:13:26','123'),(1,'singleDB',2,'2007-06-01 15:13:26','test123'),(2,'singleDB',1,'2007-06-01 15:13:37','123'),(2,'singleDB',2,'2007-06-01 15:13:37','test123'),(3,'singleDB',1,'2007-06-01 15:23:06','123'),(3,'singleDB',2,'2007-06-01 15:23:06','test123'),(4,'singleDB',1,'2007-06-01 15:23:07','123'),(4,'singleDB',2,'2007-06-01 15:23:07','test123'),(5,'singleDB',1,'2007-06-01 15:25:09','123'),(5,'singleDB',2,'2007-06-01 15:25:09','test123'),(6,'singleDB',1,'2007-06-01 15:25:53','123'),(6,'singleDB',2,'2007-06-01 15:25:53','test123');

INSERT INTO ox_images VALUES ('468x60.gif','GIF89a?<\0?\0\0uuu???DDD????????000???   eee???UUU??????\0\0\0!?\0\0\0\0\0,\0\0\0\0?<\0\0???I??8???(?di?h??l?p,?tm?x??|????cH\nP?\"?(\nB??X6?\r??JZ?P??$?Q?z?nW\Z??$0????$???\0bnuqz\'	B?C	????.?s\"	?x???x\0???E?$\0???????A?#\n??C??????D?!~???????q?\n?w?????????6???v??E???>?#?C.p?S?8t\0QR?5xs???(????A?D\"?0\\<?N??R???$?81le??d(S?[4?\0 @@??E2???I(\0?P??0?\0?s0??d??a?Y?????\0?\"$?\\	}???H??\\h???g??t??^?{,;??2??<?T	?5P?\Z?hk^S?\04??	??@6?\'??a8?\0.??y-??S?F?e+8j7???<???CG?Q?w?V???J?!??\0Kw?A?n6?E???}C??_il \"?5H?l???\00 \0s??/?l??e!????u???k@??t?x?PmX?i@?@??ur??C??BV?I?!???$?? [?B?fP8?O?\0??7??-?i??9?F?0?y?+???D????ei?n??\nG??AahZ???=?X?$?B?3f(qu9b?Z??.???A\Z*j???(?wTp\0Q???\"(?H}YI?C?y?j?9?*????t\0?B??,\nr*z??????\r?\0}?e@??r?\Z?v?z?N??M@?W?jp????&??^p,r?t?\ZlJ\n~??d?T\0?c?>??\0?b=??kqz\n???p?? ?q?8?????????\Z??4?gWP.?q??I\0$O?????;?l?????5Wm??CH?30Q?z?\\??Z???]?A?mm5?[?6?ig??\"?4\0c????v??Ml?o[-?\0??l/????\rK\0$np?(??\rK?????(R]x?X?&???\0g??????2@???nW??n???!u???.j??~zU??.??<Q?~?v??<?s?????+??9??????O???D?????=??/[z??smT? ??\Z??c?+\Z??\n?l???????[[?&0???l>x?\Z(??D@??@?A??]???-?$?!N?{?B?	??x?\\L?#??~~?a??*\Z*???????U?aK]?J?/???w? ??C\r\\???W??F??1Y?? e-???s\0?^F(#?qtt@%?x??Z?I?X??%????DC2??*?Nf?a?4?\"\'???H?wM?@????q}?t?P????CR@6(_(EY?G??K?)X?)b\0???O??J8l?.?4J?<&????0??I ?3?\r65?M?(?\0f?&5{??n??c+??J xS?)R?;;P???\"?:?S?}X@V??3#5O?B???z??\r 4?&\n?U??t\rt??Y?9?Q*???-?y??\"?#??N:?	}??URA?q????hD%;?J?E8L??j?|n vC?\ZH?4??9@S???zu?^??!P8K??k?1? ?%???%??.?x?,?X?=????TT?d?g?zE.?#>IH?]%G??s?_?Z5o>?s?S?Zl?RC???J5?8??Cc??6K?M\0??l?,?? ]?\0?Cj;\0(4\0B?*?:?gv?G?E?msF?Z\n,b??lGg??S???6?Z?Y%?)LN???Lw???MpaQ$dN?z?ZR?;?? Qd=??	v@???U?(?f??EUQ?^???[?X=p???I?K?\\:?I?	??N???AXy?8%Gl-?}?-?UW??,?u?-?N?\0#??h]/<?NtXSUa?,}??v?1?x??e?P??\r????.m.o??,s?[????d??	?[A?2(c)?%%?f0?|??:?g6??N?\n???F;\Z\0\0;','2007-05-17 12:01:02');

INSERT INTO ox_placement_zone_assoc VALUES (1,1,1),(2,1,2),(3,2,3);

INSERT INTO ox_trackers VALUES (1,'Sample Tracker','',1,3,3,0,4,1,'f','js','','2007-06-01 15:09:47');

INSERT INTO ox_variables VALUES (1,1,'boo','Sample number','numeric',NULL,0,0,0,'var boo = \\\'%%BOO_VALUE%%\\\'','f','2007-06-01 15:09:47'),(2,1,'foo','Sample string','string',NULL,0,0,0,'var foo = \\\'%%FOO_VALUE%%\\\'','f','2007-06-01 15:09:47');

INSERT INTO ox_zones VALUES (1,2,'Publisher 1 - Default','',0,3,'',468,60,'','','','',0,'f',0,'',NULL,NULL,'',NULL,NULL,'2007-04-27 15:37:19',0,0,0,'',NULL,0,NULL,'CPM'),(2,2,'Agency Publisher 1 - Default','',0,3,'',468,60,'','','','',0,'f',0,'',NULL,NULL,'',NULL,NULL,'2007-05-15 13:41:44',0,0,0,'',NULL,0,NULL,'CPM');
