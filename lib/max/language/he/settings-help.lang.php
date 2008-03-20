<?php

/*
+---------------------------------------------------------------------------+
| OpenX v${RELEASE_MAJOR_MINOR}                                                                |
| =======${RELEASE_MAJOR_MINOR_DOUBLE_UNDERLINE}                                                                |
|                                                                           |
| Copyright (c) 2003-2008 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

// Settings help translation strings
$GLOBALS['phpAds_hlp_dbhost'] = "
        ציין את השרת המארח את בסיס הנתונים של ".$phpAds_dbmsname." שאיליו אתה מנסה להתחבר";

$GLOBALS['phpAds_hlp_dbport'] = "
        ציין את מספר המבוא (port) של בסיס הנתונים ".$phpAds_dbmsname." שאליו אתה מנסה להתחבר. ברירת המחדל של מאגר ".$phpAds_dbmsname." היא <i>".
		($phpAds_dbmsname == 'MySQL' ? '3306' : '5432')."</i>.
		";
				
$GLOBALS['phpAds_hlp_dbuser'] = "
        ציין את שם המשתמש של שרת הנתונים בו ".$phpAds_productname." צריכה להשתמש כדי להתחבר ל".$phpAds_dbmsname." .
		";
		
$GLOBALS['phpAds_hlp_dbpassword'] = "
        ציין את הסיסמא שבה ".$phpAds_productname." צריכה להשתמש כדי להתחבר ל".$phpAds_dbmsname." .
		";
		
$GLOBALS['phpAds_hlp_dbname'] = "
        ציין את שם בסיס הנתונים שבו ".$phpAds_productname." תאחסן את הנתונים שלה. חשוב שבסיס הנתונים היא כבר קיים על השרת. ".$phpAds_productname." <b>לא</b> תיצור בסיס נתונים אם הוא לא נמצא.";
		
$GLOBALS['phpAds_hlp_persistent_connections'] = "
        השימוש בחיבור מתמשך יכול להאיץ את ".$phpAds_productname." בצורה משמעותית ואפילו להפחית מהעומס על השרת. אבל יש בזה חסרון מסוים, שכן באתרים עמוסים במבקרים העומס על השרת עשוי לגדול ולהפוך אפילו לכבד יותר מזה הנוצר בחיבור רגיל. אם תשתמש בחיבור רגיל או מתמשך יהיה תלוי במספר המבקרים והחומרה שבשימוש. אם ".$phpAds_productname." משתמשת ביותר מדי משאבים, 
מומלץ לבחון קביעה זו בעדיפות ראשונה.
		";
		
$GLOBALS['phpAds_hlp_insert_delayed'] = "
        ".$phpAds_dbmsname." נועלת את הטבלה בזמן שהיא משחילה נתונים. אם יש לך מבקרים רבים באתר, אפשר ש".$phpAds_productname." תצטרך לחכות לפני השחלת הנתון הבא כיוון שבסיס הנתונים נעול. אם משתמשים בהשהיית השחלה, לא תצטרך לחכות והנתונים יושחלו במועד הראשון שיתפנה בבסיס הנתונים כאשר הוא אינו עסוק.
		";
		
$GLOBALS['phpAds_hlp_compatibility_mode'] = "
      אם יש לך בעיה בשילוב ".$phpAds_productname." עם מוצר תוכנה אחר, זה עשוי לעזור אם תסמן את מצב תאימות בסיס הנתונים. אם אתה משתמש בשליפת באנר מקומית (local mode) וסעיף זה מסומן לתאימות, ".$phpAds_productname." תשאיר את מצב החיבור לבסיס הנתונים בדיוק כפי שהוא היה לפני ש".$phpAds_productname." הופעלה. 
		זו אופציה איטית יותר (רק במעט) ולכן היא מבוטלת כברירת מחדל.
		";
		
$GLOBALS['phpAds_hlp_table_prefix'] = "
     אם בסיס הנתונים שבו ".$phpAds_productname." משתמשת משותף עם עוד יישומים, מומלץ להוסיף קידומת לשמות הטבלאות. כמו כן, אם נעשה שימוש בכמה התקנות של ".$phpAds_productname."
 באותו בסיס נתונים, לעיך להבטיח שהקידומות ייחודיות לכל התקנה.
		";
		
$GLOBALS['phpAds_hlp_tabletype'] =
$GLOBALS['phpAds_hlp_table_type'] = "
        ".$phpAds_dbmsname." תומכת במספר סוגי טבלה. לכל טבלה יש את התכונות המחיודות לה, וחלקן יכולות להמהיר את ".$phpAds_productname." משמעותית. MyISAM היא ברירת המחדש הקיימת בכל התקנה של ".$phpAds_dbmsname.". סוגים אחרים אפשר שלא קיימים על השרת שלך.
		";		
		
$GLOBALS['phpAds_hlp_url_prefix'] = "
        ".$phpAds_productname." צריכה לדעת היכן היא ממוקמת בשרת כדי לתפקד כראוי. עליך לציין את כתובת ה-URL של התיקייה בה ".$phpAds_productname." מותקנת, לדוגמא: <i>http://www.your-url.com/".$phpAds_productname."</i>.";
		
$GLOBALS['phpAds_hlp_my_header'] =
$GLOBALS['phpAds_hlp_my_footer'] = "
        כאן אתה צריך לרשום את מסלול הקובץ המצביע אל הכותרת (לדוגמא: /home/login/www/header.htm) 
       כדי שתוכל להציג כותרת ו/או תחתית בכל עמוד של ממשק הניהול.
        ניתן להכיל טקסט או קוד HTML בקבצים אלה (כאשר אתה משתמש ב-HTML הימנע משימוש בתגים כמו <body> או <html>).
		";
		
$GLOBALS['phpAds_hlp_content_gzip_compression'] = "
	בהפעלת דחיסת תוכן מסוג	GZIP תופחת כמות הנתונים הנשלחת מהשרת אל הדפדפן בכל פעם שעמוד המנהלה עולה.
כדי לאפשר זאת, על השרת צריכה להיות מותקנת גירסת PHP 4.0.5 ומעלה, עם הרחבת GZIP מותקנת.
		";
		
$GLOBALS['phpAds_hlp_language'] = "
       ציין את השפה שתשמש כברירת מחדל עבור ".$phpAds_productname.". שפה זו תשמש כשפה הראשונית במערכת הניהול של האחראי והמפרסמים. אנא שים לב: אתה יכול לקבוע שפה שונה לכל מפרסם בנפרד מתוך ממשק הניהול, ולאפשר לכל מפרסם לקבוע את השפה בעצמו.		";
		
$GLOBALS['phpAds_hlp_name'] = "
    ציין את השם שאתה רוצה להעניק ליישום זה. משפט זה יופיע בכל עמודי ממשק המשתמש (מנהל ומפרסם). אם תשאיר את השדה ריק (ברירת מחדל), יופיע הלוגו של ".$phpAds_productname." במקום זאת.
		";
		
$GLOBALS['phpAds_hlp_company_name'] = "
       בשם זה ייעשה שימוש באימיילים ש-".$phpAds_productname." שולחת.
		";
		
$GLOBALS['phpAds_hlp_override_gd_imageformat'] = "
        ".$phpAds_productname." בדרך כלל בודקת אם ספריית GD מותקנת על השרת, ובאיזה מתקונת קובץ תמונה קיימת תמיכה באותה גירסה. אולם, אפשר שזיהוי זה לא יהיה מדויק או שגוי, כיוון שכמה גירסאות של PHP אינן מאפשרות זיהוי של פורמט תמונה נתמך.
אם ".$phpAds_productname." נכשלת בזיהוי האוטומטי של פורמט התמונה הנכון, אתה יכול לציין פורמט זה. האפשרויות הן: none, png, jpeg, gif.
		";
		
$GLOBALS['phpAds_hlp_p3p_policies'] = "
    כדי ש-".$phpAds_productname." תפעיל פוליסת אבטחת פרטיות מסוג P3P עליך לסמן אופציה זו.		";
		
$GLOBALS['phpAds_hlp_p3p_compact_policy'] = "
       הפוליסה הקומפקטית שתשלח בצירוף העוגיה (קוקיס). הקביעה הראשונית היא: 'CUR ADM OUR NOR STA NID', המאפשרת ל-Internet Explorer גירסה 6 לקבל את הקוקיס ש-".$phpAds_productname." עושה בהן שימוש. אם אתה רוצה, אתה יכול לשנות קביעות אלה כדי להתאים אותן למדיניות הפרטיות באתר שלך.
		";
		
$GLOBALS['phpAds_hlp_p3p_policy_location'] = "
      אם אתה רוצה להשתמש בפוליסת פרטיות מלאה, אתה יכול לציין את מיקומה.
		";
		
$GLOBALS['phpAds_hlp_log_beacon'] = "
	אתתים (Beacons) הינן תמונות קטנות ובלתי נראות הממוקמות בעמוד שבו הבאנר נצפה. אם אתה מפעיל תכונה זו, ".$phpAds_productname." תשתמש בתמוניות  אלה לספירת החשיפות שהבאנר זכה להן. אם תכבה תכונה זו, החשיפות יחשבו על פי השליפה של הבאנר. זה פחות מדויק, שכן זה אינו מבטיח שהגולש אכן זכה לצפות בבאנר על המסך.
		";
		
$GLOBALS['phpAds_hlp_compact_stats'] = "
       בהתחלה ".$phpAds_productname." עשתה שימוש מסיבי בתיעוד, אשר מטבעו מכיל כמות עצומה של פרטים, אך גם מכביד ביותר על בסיס הנתונים. זה עלול ליצור בעיה רצינית באתרים עמוסי מבקרים, וכדי להתגבר על בעיה זו ".$phpAds_productname." תומכת גם במתקונת חדשה של סטטיסטיקה - סטטיסטיקה קומפקטית, אשר מפחיתה משמעותית את העומס על השרת, אך גם פחות מפורטת כמובן. סטטיסטיקה קומפקטית זו מתעדת רק סטטיסטיקה יומית. אם אתה זקוק לתיעוד בכל שעה, כבה תכונה זו.		";
		
$GLOBALS['phpAds_hlp_log_adviews'] = "
       בדרך כלל כל החשיפות מתועדות. אם אינך רוצה באיסוף נתונים אודות החשיפות, כבה אופציה זו.	";
		
$GLOBALS['phpAds_hlp_block_adviews'] = "
	אם המבקר יטעין מחדש את העמוד,  תירשם חשיפה על ידי ".$phpAds_productname." בכל פעם. תכונה זו משמשת לוידוי שרק חשיפה אחת נרשמת לכל באנר ייחודי במהלך הזמן שתציין בשניות.לדוגמא: אם תקבע נתון זה ל-300 שניות, ".$phpAds_productname." תתעד חשיפות של אותו הבאנר רק אם הוא לא יוצג שנית בפני אותו מבקר במהלך 5 דקות. תכונה זו פועלת רק אם התכונה <i>השתמש באתתים לתיעוד חשיפות</i> מופעלת והדפדפן מותאם לקבלת קוקיס.
		";
		
$GLOBALS['phpAds_hlp_log_adclicks'] = "
    בדרך כלל כל ההקלקות מתועדות, אם אינך רוצה לצבור סטטיסטיקה אודות כמות ההקלקה על באנרים, אתה יכול לכבות אופציה זו.		";
		
$GLOBALS['phpAds_hlp_block_adclicks'] = "
	אם מבקר הקליק כפולות על באנר תירשם הקלקה אחת על ידי ".$phpAds_productname." 
		בכל פעם. תכונה זו באה להבטיח שרק קליק אחד נרשם בכל פעם עבור אותו באנר ייחודי ולמספר השניות שתציין. לדוגמא: אם תקבע נתון זה ל-300 שניות, ".$phpAds_productname." תתעד הקלקות אם המבקר לא הקליק על אותו הבאנר במהלך ה-5 דקות  האחרונות. תכונה זו פעילה רק אם הדפדפן מקבל קוקיס.
		";
		
$GLOBALS['phpAds_hlp_log_source'] = "
		אם אתה משתמש בפרמטרים של המקור בקוד השליפה של הבאנר, אתה גם יכול לשמור מידע זה במאגר הנתונים, כך שתוכל לראות סטטיסטיקה כיצד כל נתוני מקור מתפקדים. אם אתה לא משתמש בפרמטרים של המקור, או אם אתה לא רוצה לשמור את המידע, אתה יכול בבטחה לבטל אופציה זו.
		";
		
$GLOBALS['phpAds_hlp_geotracking_stats'] = "
		אם אתה משתמש במאגר גיאוגרפי, אתה יכול גם לשמור את המידע גיאוגרפי במאגר הנתונים. אם תאפשר אופציה זו, תוכל לראות סטטיסטיקה אודות המקור ממנו מגיעים המבקרים, וכיצד של באנר מתפקד במדינה מסוימת.
		אופציה זו תקפה רק אם אתה משתמש בסטטיסטיקה טקסטואלית (מבוארת, verbose).
		";
				
$GLOBALS['phpAds_hlp_log_hostname'] = "
		אם אתה רוצה לשמור את שם השרת או כתובת ה-IP של כל מבקר בתוך הסטטיסטיקה, אתה יכול לאפשר אופציה זו. איחסון המידע יאפשר לך לראות איזה שרת משחרר הכי הרבה באנרים. אופציה זו אפשרית רק בשימוש סטטיסטיקה טקסטואלית (verbose).
		";
		
$GLOBALS['phpAds_hlp_log_iponly'] = "
		שמירת שם ספקית השירות של המבקר לוקחת מקום רב במאגר הנתונים. אם אתה מאפשר תכונה זו,  ".$phpAds_productname." עדיין תשמור מידע אודות השירות/מארח, אך תשתמש בפחות מקום עבור ה-IP בלבד. אופציה זו אינה אפשרית אם שם השירות אינו מונפק בידי השרת או ".$phpAds_productname.", כיוון שבמקרה זה כתובת ה- תישמר תמיד.
		";
				
$GLOBALS['phpAds_hlp_reverse_lookup'] = "
		שם השירות נקבע בדרך כלל דרך שם השרת, אך במקרים מסוימים אפשרות זו כבויה. אם אתה רוצה להכליל את שם השירות בתוך הגבלות התפוצה ו/או לשמור סטטיסטיקה אודות נתון זה, והשרת אינו מנפיק את המידע, עליך לכבות אופציה זו. קביעת שם השירות גוזל זמן יקר; עובדה שתאט את הנפקת הבאנרים.
		";
		
$GLOBALS['phpAds_hlp_proxy_lookup'] = "
	ישנם גולשים המשתמשים בשרת פרוקסי (proxy) לגישת האינטרנט שלהם. במקרה זה ".$phpAds_productname." תרשום את כתובת ה-IP או השם של שרת הפרוקסי במקום זה של הגולש. אם תאפשר תכונה זו, ".$phpAds_productname." תנסה למצוא את כתובת ה-IP או ספקית השירות שמאחורי שרת הפרוקסי. אם אין אפשרות למצוא את הכתובת המדויקת של הגולש, היא תשתמש בכתובת של הפרוקסי במקום זאת. אופציה זו אינה מסומנת כברירת מחדל, כיוון שהיא מאטה את תהליך התיעוד.
		";
		
$GLOBALS['phpAds_hlp_auto_clean_tables'] = 	"";
$GLOBALS['phpAds_hlp_auto_clean_tables_interval'] = "
		אם אופציה זו מסומנת, הסטטיסטיקה הנאספת תימחק אוטומטית לאחר פרק הזמן שקבעת כאן. לדוגמא, אם רשמת 5 שבועות, סטטיסטיקה ישנה יותר מ-5 שבועות תימחק אוטומטית
		";
$GLOBALS['phpAds_hlp_auto_clean_userlog'] = 	"";	
$GLOBALS['phpAds_hlp_auto_clean_userlog'] = 
$GLOBALS['phpAds_hlp_auto_clean_userlog_interval'] = "
		אופציה זו תמחק אוטומטית רישום יומן משתמש שהוא ישן יותר ממספר השבועות שקבעת כאן.
		";
		
$GLOBALS['phpAds_hlp_geotracking_type'] = "
		ניתוב גיאוגרפי של ".$phpAds_productname." להפוך את כתובת ה-IP של המבקר למידע גיאוגרפי. בהתבסס על מידע זה ניתן לקבוע הגבלות בארנים או לאגור מידע מעקב מאיזו מדינה מתקבלת חשיפה מוגברת או הקלקות. אם ברצנוך לאפשר זאת, יש לבחור באיזה סוג של מאגר נתונים להשתמש. ".$phpAds_productname." תומכת כעת במאגרי הנתונים של IP2Country 
		ו-<a href='http://www.maxmind.com/?rId=phpadsnew2' target='_blank'>GeoIP</a>.
		";
		
		
$GLOBALS['phpAds_hlp_geotracking_location'] = "
		להוציא מקרים בהם יש ברשותך מודול GeoIP של Apache, תצטרך להורות ל-".$phpAds_productname." tהיכן נמצא מאגר הנתונים לניתוב גיאוגרפי. מומלץ תמיד לשמאגר זה יהיה מחוץ לספריית השורש בשרת, אחרת מבקרים יוכלו להוריד אליהם בסיס זה.
		";
		
$GLOBALS['phpAds_hlp_geotracking_cookie'] = "
		המרת כתובת ה-IP למיקום גיאוגרפי לוקחת זמן. כדי למנוע מ-".$phpAds_productname." לבצע זאת בכל פעם שבאנר מונפק, ניתן לשמור את המידע בקוקי. הוא נתון הקוקי נמצא, ".$phpAds_productname." תשתמש במידע זה במקום תהליך המרת הכתובת.
		";
				
$GLOBALS['phpAds_hlp_ignore_hosts'] = "
      אם אינך רוצה למנות הקלקות וחשיפות מתוך מחשב מסוים, אתה יכול להוסיף אותו למערך הזה. אם איפשרת תיעוד כתובת גולש (Reverse lookup) תוכל להוסיף שם מתחם וכתובת IP,  אחרת תוכל להשתמש רק בכתובת IP. ניתן להשתמש גם בתווים משלימים (wildcards כמו למשל '*.altavista.com' או '192.168.*').
		";
		
$GLOBALS['phpAds_hlp_begin_of_week'] = "
      עבור רוב הגולשים השבוע מתחיל ביום שני, אנחנו הישראלים/יהודים מתחילים ביום ראשון, וכאן המקום לקבוע זאת.	";
		
$GLOBALS['phpAds_hlp_percentage_decimals'] = "
        ציין כמה ספרות לאחר הנקודה להציג נתונים בעמודי הסטטיסטיקה.
		";
		
$GLOBALS['phpAds_hlp_warn_admin'] = "
        ".$phpAds_productname." יכולה לשלוח אימייל אם לקמפיין נותר מספר מועט של חשיפות או הקלקות מוקצבות. זה מופעל כברירת מחדל.
		";
		
$GLOBALS['phpAds_hlp_warn_client'] = "
        ".$phpAds_productname." יכולה לשלוח למפרסם אימייל אם אחד הקמפיינים שלו מגיע לסיום המוקצב של חשיפות או הקלקות. נתון זה מופעל כברירת מחדל.		";
		
$GLOBALS['phpAds_hlp_qmail_patch'] = "
		כמה גירסאות של qmail מכילות באג, אשר גורם לאימייל שנשלח בידי	".$phpAds_productname." להציג את הכותרות בתוך גוף המכתב. אם תאפשר קביעה זו, ".$phpAds_productname." תשלח אימייל בתצורה תואמת למתקונת qmail.
		";
		
$GLOBALS['phpAds_hlp_warn_limit'] = "
       הסף שממנו".$phpAds_productname." תתחיל לשלוח איתותי אזהרה באימייל. נתון זה נקבע על 100 כברירת מחדל.	";

$GLOBALS['phpAds_hlp_allow_invocation_plain'] = 
$GLOBALS['phpAds_hlp_allow_invocation_js'] = 
$GLOBALS['phpAds_hlp_allow_invocation_frame'] = 
$GLOBALS['phpAds_hlp_allow_invocation_xmlrpc'] = 
$GLOBALS['phpAds_hlp_allow_invocation_local'] = 
$GLOBALS['phpAds_hlp_allow_invocation_interstitial'] = 
$GLOBALS['phpAds_hlp_allow_invocation_popup'] = "
		קביעות אלה מאפשרות שליטה על סוג השליפה של באנרים. אם אחד מסוגי השליפה האלה מבוטל, הוא לא יהיה מוצג כאפשרות בעמוד מחולל הקוד. חשוב: שיטות השליפה יעבדו אפילו אם הן מבוטלות - הן רק לא תוצגנה בעמודי הכנת הקוד השתול.	";
		
$GLOBALS['phpAds_hlp_con_key'] = "
        ".$phpAds_productname." מכילה מערכת שחזור חזקה, העושה שימוש בבחירה ישירה. לפרטים נוספים עיין במדריך המשתמש. באמצעות אופציה זו תוכל להפעיל מילות מפתח כתנאי. מופעל כברירת מחדל.		";
		
$GLOBALS['phpAds_hlp_mult_key'] = "
     אם אתה משתמש בבחירה ישירה לתצוגת באנרים, תוכל לציין מילת מפתח אחת או יותר עבור כל באנר. אופציה זו דורשת הפעלה אם אתה רוצה לציין יותר ממילה אחת. מופעלת כברירת מחדל.	";
		
$GLOBALS['phpAds_hlp_acl'] = "
      אם אתה לא משתמש בהגבלות הפצה, תוכל להשבית אופציה זו. זה יאיץ את ".$phpAds_productname." במעט.
		";
		
$GLOBALS['phpAds_hlp_default_banner_url'] = 
$GLOBALS['phpAds_hlp_default_banner_target'] = "
       אם ".$phpAds_productname." לא יכולה להתחבר לבסיס הנתונים, או שאינה מוצאת באנר תואם כלל, לדוגמא - אם בסיס הנתונים קרס או נמחק, היא לא תציג מאומה. יש משתמשים המעדיפים לציין באנר חלופי, אשר יוצג כברירת מחדל במקרים אלו. הבאנר שיצויין כאן לא יתועד מבחינת חשיפה או הקלקה, ולא יעשה בו שימוש אם יש עדיין באנרים פעילים בבסיס הנתונים. אופציה זו כבויה כברירת מחדל.";
		
$GLOBALS['phpAds_hlp_delivery_caching'] = "
		בכדי להמהיר הנפקת באנרים, ".$phpAds_productname." משתמשת בזכרון מטמון הכולל את כל המידע הנדרש להנפקת הבאנר לגולש. מטמון זיכרון זה נשמר במאגר הנתונים כברירת מחדל, אך להמהרה גדולה יותר אפשר אפילו למשור אותו כקובץ או בזכרון משותףץ זכרון משותף הוא המהיר מכולם, קבצים מהירם גם כן. מומלץ שלא לבטל את זכרון המטמון אחרת הביצועים ייגרעו בהרבה.
		";
		
		
$GLOBALS['phpAds_hlp_type_sql_allow'] = 
$GLOBALS['phpAds_hlp_type_web_allow'] = 
$GLOBALS['phpAds_hlp_type_url_allow'] = 
$GLOBALS['phpAds_hlp_type_html_allow'] = 
$GLOBALS['phpAds_hlp_type_txt_allow'] = "
        ".$phpAds_productname." יכולה להשתמש בסוגים שונים של באנרים ולאחסן אותם בדרכים שונות. שתי האופציות הראשונות משמשות לאיחסון מקומי על השרת. אתה יכול להשתמש בממשק המנהל להעלאת באנר ו-".$phpAds_productname." תשמור אותו בבסיס הנתונים מסוג SQL או בתוך תיקייה בשרת. תוכל להשתמש גם בבאנר המאוחסן בשרת חיצוני או בקוד HTML שיוצר באנר.		";
		
$GLOBALS['phpAds_hlp_type_web_mode'] = "
  אם אתה רוצה להשתמש בבאנרים המאוחסנים על השרת, עליך לעצב את הקביעה הזו. אם אתה רוצה לאחסן את הבאנרים בתיקיה מקומית, קבע אופציה זו ל<i>תקייה מקומית</i>. אם אתה רוצה לאחסן את הבאנר על שרת קבצים (FTP) חיצוני, קבע אופציה זו ל<i>שרת FTP חצוני</i>. בשרתים מסוימים אפשר שתרצה להשתמש באופציית FTP אפילו על השרת המקומי.
		";
		
$GLOBALS['phpAds_hlp_type_web_dir'] = "
       ציין את התיקייה שלתוכה ".$phpAds_productname." צריכה להעתיק את הבאנר שתעלה. תיקייה זו צריכה להיות במצב אפשרי לכתיבה על ידי PHP, שזה אומר שאתה צריך לשנות את היתרי הגישה ביוניקס (chmod) למצב כתיבה לכל. התיקייה שתציין צריכה להיות במסלול הראשוני של האתר (תקיית שורש), וצריכה להיות נגישה לטיפול ישיר בקבצים בידי השרת. אנא אל תרשום קן נטוי בסיומת (סלאש נטוי [/]). אתה חייב לציין אופציה זו רק אם קבעת את שיטת האיחסון ל<i>תקייה מקומית</i>.
		";
		
$GLOBALS['phpAds_hlp_type_web_ftp_host'] = "
	אם קבעת את שיטת האיחסון ל<i>שרת FTP חיצוני</i> עליך לציין את כתובת ה-IP או שם המתחם (דומיין) של שרת ה-FTP להיכן ש-".$phpAds_productname." תצטרך להעתיק את הבאנרים שיועלו.	";
      
$GLOBALS['phpAds_hlp_type_web_ftp_path'] = "
	אם קבעת את שיטת האיחסון ל<i>שרת FTP חיצוני</i> עליך לציין את התיקייה על השרת הזה, היכן ש".$phpAds_productname." תצטרך להעתיק את הבאנרים שיועלו.	";

$GLOBALS['phpAds_hlp_type_web_ftp_user'] = "
		אם קבעת את שיטת האיחסון ל<i>שרת FTP חיצוני</i> עליך לציין את שם המשתמש שבו ".$phpAds_productname." צריכה להשתמש כדי להתחבר לשרת ה-FTP החיצוני.
		";
		
$GLOBALS['phpAds_hlp_type_web_ftp_password'] = "
		אם קבעת את שיטת האיחסון ל<i>שרת FTP חיצוני</i> עליך לציין את הסיסמא שבה ".$phpAds_productname." צריכה להשתמש כדי להתחבר לשרת ה-FTP החיצוני.";

$GLOBALS['phpAds_hlp_type_web_url'] = "
       אם אתה מאחסן את הבאנרים על שרת אינטרנט, ".$phpAds_productname." צריכה לדעת איזו כתובת  URL ציבורית קשורה לתיקייה שציינת למטה. נא לא לרשום סלאש סופי  (/).";

$GLOBALS['phpAds_hlp_type_html_auto'] = "
       אם אופציה זו דלוקה ".$phpAds_productname." תשנה אוטומטית את קוד הבאנרים מסוג  HTML כדי לאפשר תיעוד הקלקות. למרות זאת, תמיד תוכל לשנות אופציה זו על בסיס פרטי של כל באנר.";
		
$GLOBALS['phpAds_hlp_type_html_php'] = "
      ניתן לאפשר ל".$phpAds_productname." להפעיל קוד PHP המוטבע בתוך באנר מסוג HTML. אופציה זו כבויה כברירת מחדל.";

$GLOBALS['phpAds_hlp_admin'] = "
       שם המשתמש של האחראי (אדמיניסטרטור). באמצעות שם זה ניתן להתחבר לממשק המנהלה.";

$GLOBALS['phpAds_hlp_pwold'] =  
$GLOBALS['phpAds_hlp_pw'] = 
$GLOBALS['phpAds_hlp_pw2'] = "
       כדי לשנות את הסיסמא של המנהל, עליך לספק את הסיסמא הקיימת למעלה. בנוסף, תצטרך לרשום את הסיסמא החדשה פעמיים, וזאת למניעת טעויות.";

$GLOBALS['phpAds_hlp_admin_fullname'] = "
        ציין את השם המלא של האחראי/מנהל. בשם זה ייעשה שימוש בשליחת סטטיסטיקה באימייל.	";

$GLOBALS['phpAds_hlp_admin_email'] = "
      כתובת האימייל של האחראי/מנהל. זו הכתובת שתופיע בשדה (מ-)  בשליחת האסטטיסטיקה באימייל.";

$GLOBALS['phpAds_hlp_admin_email_headers'] = "
      אתה יכול לשנות את כותרת האימייל שבה ".$phpAds_productname." משתמשת למשלוח אימייל.";

$GLOBALS['phpAds_hlp_admin_novice'] = "
      אם אתה רוצה לקבל אתראה לפני מחיקת מפרסם/ים, קמפיין או באנרים, סמן אופציה זו.	";

$GLOBALS['phpAds_hlp_client_welcome'] = 
$GLOBALS['phpAds_hlp_client_welcome_msg'] = "
     אם תפעיל תכונה זו, תוצג הודעה הקדמה בעמוד הראשון שכל מפרסם יראה בעת החיבור למערכת. אתה יכול להתאים אותו אישית לצרכיך על ידי עריכת הקובץ 'welcome.html' המצוי בתיקיית 'admin/templates'. אפשר שתרצה להכיל שם את שם החברה, קשר, הלוגו שלך, קישור לעמודי מחירים וכדומה.";

$GLOBALS['phpAds_hlp_updates_frequency'] = "
		אם אתה רוצה לבדוק האם יצאה גירסה חדשה של ".$phpAds_productname." אתה יכול לאפשר פונקציה זו. ניתן לקבוע את המרווחים שבין בדיקה אחת לשניה, בהם  ".$phpAds_productname." תבצע התחברות לשרת העדכונים. אם תימצא גירסה חדשה, יקפוץ לפניך חלון עם המידע הדרוש.";

$GLOBALS['phpAds_hlp_userlog_email'] = "
	אם אתה רוצה לשמור עותק של האימייל הנשלח באמצעות ".$phpAds_productname." אתה יכול לאפשר פונצקיה זו. הודעות האימייל נשמרות בתיעוד המשתמש.	";
$GLOBALS['phpAds_hlp_userlog_priority'] = "
		כדי לוודא שחישובי הקדימויות רצים נכונה, אתה יכול לשמור דוח אודות החישוב שנעשה בכל שעה. הדוח כולל את הפרופיל הנצפה וכמה קדימות מנותבת לכל באנר. המידע עשוי להיות שימושי אם אתה רוצה להגיש אותו בצירוף קבילת באג אודות פעילות הקצאת הקדימויות. הדוחות נשמרים בתוך תיעוד המשתמש.	";

$GLOBALS['phpAds_hlp_userlog_autoclean'] = "
		כדי להבטיח שבסיס הנתונים דולל נכונה, ניתן לשמור דיווח אודות מה אירע במהלך פעולה זו. מידע זה נשמא ביומן המשתמש.
		";

$GLOBALS['phpAds_hlp_default_banner_weight'] = "
		אם אתה רוצה להשתמש במשקל באנר התחלתי גבוה יותר, אתה יכול לקבוע את זה כאן. ברירת המחדל היא 1.";

$GLOBALS['phpAds_hlp_default_campaign_weight'] = "
		אם אתה רוצה להשתמש במשקל קמפיין התחלתי גבוה יותר, אתה יכול לקבוע את המשקל המבוקש כאן. ברירת המחדל היא 1.	";

$GLOBALS['phpAds_hlp_gui_show_campaign_info'] = "
		אם אופציה זו מסומנת, מידע נוסף עבור כל קמפיין יוצג בעמוד <i>סקירת קמפיין</i> . מידע נוסף זה כולל את מספר החשיפות הנותרות, תאריך ההפעלה וקביעות הקדימויות.";

$GLOBALS['phpAds_hlp_gui_show_banner_info'] = "
		אם אופציה זו פעילה, מידע נוסף אודות כל באנר יוצג בעמוד <i>סקירת באנרים</i> . מידע נוסף זה כולל את עמוד המטרה (אליו יילקח הגולש לאחר הקלה), מילות מפתח, גודל הבאנר ומשקלו.";

$GLOBALS['phpAds_hlp_gui_show_campaign_preview'] = "
	אם אופציה זו מופעלת יוצגו כל הבאנרים בצורה ממשית בעמוד <i>סקירת באנרים</i> . אם האופציה כבויה, עדיין יהיה אפשר לצפות בכל באנר על ידי לחיצה על המשולש הסמוך אליו בעמוד <i>סקירת באנרים</i>.
		";

$GLOBALS['phpAds_hlp_gui_show_banner_html'] = "
		אם אופציה זו פעילה יוצג באנר HTML בצורה מציאותית, וזאת במקום קוד HTML פשוט. אופציה זו כבויה כברירת מחדל כיוון שבאנרים מסוג HTML עשויים להתנגש עם ממשק המשתמש. אם אופציה זו כבויה עדיין אפשרי לצפות בבאנר HTML ממשי באמצעות לחיצה על כפתור <i>הצג באנר</i> הסמוך לקוד ה-HTML.";

$GLOBALS['phpAds_hlp_gui_show_banner_preview'] = "
		אם אופציה זו פעילה יוצג הבאר בכותרת של  העמודים <i>תכונות הבאנר</i>, 
		<i>אופציות תפוצה</i> ו-<i>אזורים מקושרים</i>. אם אופציה זו מבוטלת, עדיין ניתן לצפות בבאנר באמצעות לחיצה על כפתור <i>הצג באנר</i> בכותרת העמודים.";

$GLOBALS['phpAds_hlp_gui_hide_inactive'] = "
	אם אופציה זו פעילה כל הבאנרים שאינם פעילים, הקמפיינים והמפרסמים, יוסתרו מהתצוגה בעמודים <i>מפרסמים ומערכות</i> ו-<i>סקירת קמפיין</i>. אם פונקציה זו פעילה, עדיין אפשרי לצפות בפריטים המוסתרים באמצעות הקלקה על כפתור <i>הצג הכל</i> שבתחתית העמודים	";

$GLOBALS['phpAds_hlp_gui_show_matching'] = "
		אם אופציה זו פעילה, הבאנר התואם ייראה בעמוד <i>באנרים מקושרים</i> , אם  <i>בחירת קמפיין</i> היא השיטה הנבחרת. זה מאפשר לראות במדויק אלו באנרים מיועדים להנפקה אם הקמפיינים מקושרים. אפשר יהיה גם לצפות בתצוגה מקדמת של הבאנרים התואמים.
		";

$GLOBALS['phpAds_hlp_gui_show_parents'] = "
		אם אופציה זו פעילה, הקמפיינים הראשיים של הבאנרים יוצגו בעמוד <i>באנרים מקושרים</i>, אם <i>בחירת באנר</i> היא השיטה הנבחרת. זה יאפשר לראות להיכן שייך כל באנר לפני קישורו. זה אומר גם שהבאנרים מקובצים בידי קמפיין-אם וכבר לא שמורים בסדר אלפביתי.
		";

$GLOBALS['phpAds_hlp_gui_link_compact_limit'] = "
		כברירת מחדל כל הבאנרים והקמפיינים הקיימים מוצגים בעמוד<i>באנרים מקושרים</i>.
	כיוון שעמוד זה יכול להיות ארוך מאוד (אם יש לך באנרים רבים), אופציה זו מאפשרת קביעת מספר מרבי של פריטים לעמוד.
		";

					

?>