disable layout
-----------
    $this->_helper->layout->disableLayout();
  
set layout
-----------
    $this->_helper->layout->setLayout("ajax");
  
not render the view phtml
-----------
    $this->_helper->viewRenderer->setNoRender(true);
  
Layout Ajax
=======
add data 
in phtml file

    $this->layout()->data= some data

add js
in phtml file

    $this->headScript()->captureStart();
    echo 'alert('script!');'
    $this->headScript()->captureEnd();

in controller

    Model_refresh::getInstance()->addjs()

Content and title (set with $this->headTitle('sometitle') in phtml file) will display as windows

windows's option 
* **notdestroy** $this->layout()->notdetroy boolean windows is not destroyed after close
* **close** $this->layout()->close script action to do after close
* **x** $this->layout()->x int position x of windows
* **y** $this->layout()->y int position y of windows
* **mod** $this->layout()->mod boolean modal windows
* **id** $this->layout()->id windows's id
* **button** $this->layout()->button boolean add button okon the windows
* **error** $this->layout()->error boolean apply the error style

image parsing

$this->image()->parse('[tag|heigth|width|funntion()]');