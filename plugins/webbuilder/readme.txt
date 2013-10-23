This is "Web builder" plugin.

1. Directupload.
    This plugin is necessary for uploading images into your pages.
    1. foldername - name of folder for images
    2. imagename  - Image name
    Example: {$directupload:foldername:filename:width}
    3. For making gallery third param name (relgall) example: {$directupload:foldername:imagename:200:relgall}
    4. Also we can add change the 'image size name' ('small', 'medium', 'large', 'crop', 'original', 'product', 'thumbnails'). 
    example: {$directupload:foldername:imagename:200:relgall:crop}.
            or:
             {$directupload:foldername:imagename:200::crop}
    5. If you don't have 'static' as last param you will have imagename_pageId
        example: {$directupload:foldername:imagename:200} result 'imagename_27'
        If you have 'static' last param you will have the same image for all pages
        {$directupload:foldername:filename:width:static}

2. Imageonly
    This plugin is necessary for inserting images on page.
    How to use it:
    First after installation click on "container" (C) where do you want to place your image.
    Then enter {$imageonly:photo:200}. The parameter 'photo' you can change. This is a name of the container for your image.
    The parameter '200' is a width of your image. You can change it too for your purposes.
    Then press 'done'. And you can see a new container (edit image) on the screen.
    Open it and you may insert or remove one image.
    For inserting image press select folder and choose your folder of images. Then you can see images on the screen.
    After that click on image what do you want add to page. You can also add image description. 
    Write the description to the image and then click on image. You can see your description in parameter <img alt="your description">
    Also you can replace your image. New image replace your previous image.
    For deleting image open your container (edit image) and click on button 'remove image'.       
    Also you can add url of external image.
    
    If you have 'static' last param you will have the same image for all pages 
    You can also put {$imageonly:photo:200:static}

3. Textonly
    This plugin is necessary for inserting text on page.
    How to use it:
    First after installation click on "container" (C) where do you want to insert your text.
    Then enter   {$textonly:uniq_name}. The parameter 'uniq_name' you can change. This is a name of the container for your text.
    Then press 'done'. And you can see a new container (edit text) on the screen.
    Open it and you may insert or remove text.  
  
    If you have 'static' last param you will have the same image for all pages 
    You can also put {$textonly:name:static}
4. Featuredonly
   This plugin is necessary for inserting featured area into the page.
    How to use it:
    First after installation click on "container" (C) where do you want to insert your featured area.
    Then enter {$featuredonly:name}

    If you have 'static' last param you will have the same image for all pages 
    You can also put {$featuredonly:name:static}
5. Galleryonly
    This plugin is necessary for inserting gallery on page.
    How to use it:
    First after installation click on "container" (C) where do you want to place your image.
    Then enter {$galleryonly:uniq_name}. The parameter 'uniq_name' you can change. This is a name of the container for your gallery.

    Then press 'done'. And you can see a new container (edit gallery) on the screen.
    Open it and you may insert or remove gallery.
    For inserting gallery press select folder and choose your folder of images. 
    In thumbnails size you must input size of pictures in your gallery.
    Also you can use crop and caption options. You need use checkboxes for this.
    Also you can replace your gallery. New gallery replace your previous gallery.

    If you have 'static' last param you will have the same image for all pages 
    You can also put {$galleryonly:name:static}

   