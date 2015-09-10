<?php 
abstract class researchs {
	static $livmax=array(1,1,1,1,1,1);
	static $cost=array();
	/**
	 * 
	 * @var array (
	 * [research]->array([0]->array([liv],[type])),
	 * [build]->array([0]->array([liv],[type])))
	 */
	static $require=array();
	static $age=0;
	static $description="";
	static $name;
}
class Rarco extends researchs {
	static $cost=array(40);
	static $description="[RARCO_DES]";
	static $age=1;
	static $name="[RARCO_NAME]";
}
class Rleadership extends researchs {
	static $livmax=array(1,5,10,20,50,100);
	static $cost=array(200,400,800,1400,2200,3200,4400,5800,7400,9200,11200,13400,15800,18400,21200,24200,27400,30800,34400,38200,42200,46400,50800,55400,60200,65200,70400,75800,81400,87200,93200,99400,105800,112400,119200,126200,133400,140800,148400,156200,164200,172400,180800,189400,198200,207200,216400,225800,235400,245200,255200,265400,275800,286400,297200,308200,319400,330800,342400,354200,366200,378400,390800,403400,416200,429200,442400,455800,469400,483200,497200,511400,525800,540400,555200,570200,585400,600800,616400,632200,648200,664400,680800,697400,714200,731200,748400,765800,783400,801200,819200,837400,855800,874400,893200,912200,931400,950800,970400,990200);
	static $age=1;
	static $name="Leadership";
	static $description="[LEADERSHIP_DES]";
	static $require=array('buid'=>array('tipe'=>COMMAND,'liv'=>1));
}
class Rstorage extends researchs {
	static $livmax=array(1,2,5,10,20,50);
	static $cost=array(20,40,80,140,220,320,440,580,740,920,1120,1340,1580,1840,2120,2420,2740,3080,3440,3820,4220,4640,5080,5540,6020,6520,7040,7580,8140,8720,9320,9940,10580,11240,11920,12620,13340,14080,14840,15620,16420,17240,18080,18940,19820,20720,21640,22580,23540,24520);
	static $age=1;
	static $name="[STORAGE]";
	static $description="[STORAGE_DES]";
}
?>