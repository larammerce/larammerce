###
#
# More info at [www.dropzonejs.com](http://www.dropzonejs.com)
#
# Copyright (c) 2012, Matias Meno
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
# THE SOFTWARE.
#
###

define 'dropzone', ['jquery', 'underscore', 'tools'], (jQuery, _, tools) ->
  noop = ->


  # The Emitter class provides the ability to call `.on()` on Dropzone to listen
  # to events.
  # It is strongly based on component's emitter class, and I removed the
  # functionality because of the dependency hell with different frameworks.
  class Emitter

    # Add an event listener for given event
    addEventListener: @::on
    on: (event, fn) ->
      @_callbacks = @_callbacks || {}
      # Create namespace for this event
      @_callbacks[event] = [] unless @_callbacks[event]
      @_callbacks[event].push fn
      return @


    emit: (event, args...) ->
      @_callbacks = @_callbacks || {}
      callbacks = @_callbacks[event]

      if callbacks
        callback.apply @, args for callback in callbacks

      return @

    # Remove event listener for given event. If fn is not provided, all event
    # listeners for that event will be removed. If neither is provided, all
    # event listeners will be removed.
    removeListener: @::off
    removeAllListeners: @::off
    removeEventListener: @::off
    off: (event, fn) ->
      if !@_callbacks || arguments.length == 0
        @_callbacks = {}
        return @

      # specific event
      callbacks = @_callbacks[event]
      return @ unless callbacks

      # remove all handlers
      if arguments.length == 1
        delete @_callbacks[event]
        return @

      # remove specific handler
      for callback, i in callbacks
        if callback == fn
          callbacks.splice i, 1
          break

      return @

  class Dropzone extends Emitter

    # Exposing the emitter class, mainly for tests
    Emitter: Emitter

    ###
    This is a list of all available events you can register on a dropzone object.

    You can register an event handler like this:

        dropzone.on("dragEnter", function() { });

    ###
    events: [
      "drop"
      "dragstart"
      "dragend"
      "dragenter"
      "dragover"
      "dragleave"
      "addedfile"
      "addedfiles"
      "removedfile"
      "thumbnail"
      "error"
      "errormultiple"
      "processing"
      "processingmultiple"
      "uploadprogress"
      "totaluploadprogress"
      "sending"
      "sendingmultiple"
      "success"
      "successmultiple"
      "canceled"
      "canceledmultiple"
      "complete"
      "completemultiple"
      "reset"
      "maxfilesexceeded"
      "maxfilesreached"
      "queuecomplete"
    ]



    defaultOptions:
      # Has to be specified on elements other than form (or when the form
      # doesn't have an `action` attribute). You can also
      # provide a function that will be called with `files` and
      # must return the url (since `v3.12.0`)
      url: null

      # can be changed to `"put"` if necessary. You can also provide a function
      # that will be called with `files` and must return the method (since `v3.12.0`).
      method: "post"

      # foo
      withCredentials: no

      # The timeout for the XHR requests in milliseconds (since `v4.4.0`).
      timeout: 30000

      # How many file uploads to process in parallel (See the
      # *Enqueuing file uploads* documentation section for more info)
      parallelUploads: 2

      # Whether to send multiple files in one request. If
      # this it set to true, then the fallback file input element will
      # have the `multiple` attribute as well. This option will
      # also trigger additional events (like `processingmultiple`). See the events
      # documentation section for more information.
      uploadMultiple: no

      # If not `null` defines how many files this Dropzone handles. If it exceeds,
      # the event `maxfilesexceeded` will be called. The dropzone element gets the
      # class `dz-max-files-reached` accordingly so you can provide visual feedback.
      maxFilesize: 256

      # The name of the file param that gets transferred.
      # **NOTE**: If you have the option  `uploadMultiple` set to `true`, then
      # Dropzone will append `[]` to the name.
      paramName: "image"

      # Whether thumbnails for images should be generated
      createImageThumbnails: true

      # In MB. When the filename exceeds this limit, the thumbnail will not be generated.
      maxThumbnailFilesize: 10

      # If `null`, the ratio of the image will be used to calculate it.
      thumbnailWidth: 120

      # The same as `thumbnailWidth`. If both are null, images will not be resized.
      thumbnailHeight: 120

      # How the images should be scaled down in case both, `thumbnailWidth` and `thumbnailHeight` are provided.
      # Can be either `contain` or `crop`.
      thumbnailMethod: 'crop'

      # If set, images will be resized to these dimensions before being **uploaded**.
      # If only one, `resizeWidth` **or** `resizeHeight` is provided, the original aspect
      # ratio of the file will be preserved.
      #
      # The `options.transformFile` function uses these options, so if the `transformFile` function
      # is overridden, these options don't do anything.
      resizeWidth: null

      # See `resizeWidth`.
      resizeHeight: null

      # The mime type of the resized image (before it gets uploaded to the server).
      # If `null` the original mime type will be used. To force jpeg, for example, use `image/jpeg`.
      # See `resizeWidth` for more information.
      resizeMimeType: null

      # The quality of the resized images. See `resizeWidth`.
      resizeQuality: 0.8

      # How the images should be scaled down in case both, `resizeWidth` and `resizeHeight` are provided.
      # Can be either `contain` or `crop`.
      resizeMethod: 'contain'

      # The base that is used to calculate the filesize. You can change this to
      # 1024 if you would rather display kibibytes, mebibytes, etc...
      # 1024 is technically incorrect, because `1024 bytes` are `1 kibibyte` not `1 kilobyte`.
      # You can change this to `1024` if you don't care about validity.
      filesizeBase: 1000

      # Can be used to limit the maximum number of files that will be handled
      # by this Dropzone
      maxFiles: null

      # Can be an object of additional parameters to transfer to the server.
      # This is the same as adding hidden input fields in the form element.
      params: { }

      # An optional object to send additional headers to the server. Eg:
      # `{ "My-Awesome-Header": "header value" }`
      headers: null

      # If true, the dropzone will present a file selector when clicked.

      # If `true`, the dropzone element itself will be clickable, if `false`
      # nothing will be clickable.
      #
      # You can also pass an HTML element, a CSS selector (for multiple elements)
      # or an array of those. In that case, all of those elements will trigger an
      # upload when clicked.
      clickable: yes

      # Whether hidden files in directories should be ignored.
      ignoreHiddenFiles: yes


      # The default implementation of `accept` checks the file's mime type or
      # extension against this list. This is a comma separated list of mime
      # types or file extensions.
      #
      # Eg.: `image/*,application/pdf,.psd`
      #
      # If the Dropzone is `clickable` this option will also be used as
      # [`accept`](https://developer.mozilla.org/en-US/docs/HTML/Element/input#attr-accept)
      # parameter on the hidden file input as well.
      acceptedFiles: null

      # **Deprecated!**
      # Use acceptedFiles instead.
      acceptedMimeTypes: null

      # If false, files will be added to the queue but the queue will not be
      # processed automatically.
      # This can be useful if you need some additional user input before sending
      # files (or if you want want all files sent at once).
      # If you're ready to send the file simply call `myDropzone.processQueue()`.
      #
      # See the [enqueuing file uploads](#enqueuing-file-uploads) documentation
      # section for more information.
      autoProcessQueue: on

      # If false, files added to the dropzone will not be queued by default.
      # You'll have to call `enqueueFile(file)` manually.
      autoQueue: on

      # If `true`, this will add a link to every file preview to remove or cancel (if
      # already uploading) the file. The `dictCancelUpload`, `dictCancelUploadConfirmation`
      # and `dictRemoveFile` options are used for the wording.
      addRemoveLinks: no

      # Defines where to display the file previews – if `null` the
      # Dropzone element itself is used. Can be a plain `HTMLElement` or a CSS
      # selector. The element should have the `dropzone-previews` class so
      # the previews are displayed properly.
      previewsContainer: null

      # This is the element the hidden input field (which is used when clicking on the
      # dropzone to trigger file selection) will be appended to. This might
      # be important in case you use frameworks to switch the content of your page.
      hiddenInputContainer: "body"

      # If null, no capture type will be specified
      # If camera, mobile devices will skip the file selection and choose camera
      # If microphone, mobile devices will skip the file selection and choose the microphone
      # If camcorder, mobile devices will skip the file selection and choose the camera in video mode
      # On apple devices multiple must be set to false.  AcceptedFiles may need to
      # be set to an appropriate mime type (e.g. "image/*", "audio/*", or "video/*").
      capture: null

      # **Deprecated**. Use `renameFile` instead.
      renameFilename: null

      # A function that is invoked before the file is uploaded to the server and renames the file.
      # This function gets the `File` as argument and can use the `file.name`. The actual name of the
      # file that gets used during the upload can be accessed through `file.upload.filename`.
      renameFile: null

      # If `true` the fallback will be forced. This is very useful to test your server
      # implementations first and make sure that everything works as
      # expected without dropzone if you experience problems, and to test
      # how your fallbacks will look.
      forceFallback: off

      # The text used before any files are dropped.
      dictDefaultMessage: "فایل مورد نظر را در این قسمت آپلود کنید"

      # The text that replaces the default message text it the browser is not supported.
      dictFallbackMessage: "مرورگر شما از این افزونه پشتیبانی نمیکند."

      # The text that will be added before the fallback form.
      # If you provide a  fallback element yourself, or if this option is `null` this will
      # be ignored.
      dictFallbackText: "از فرم زیر برای آپلود استفاده کنید."

      # If the filesize is too big.
      # `{{filesize}}` and `{{maxFilesize}}` will be replaced with the respective configuration values.
      dictFileTooBig: "فایل مورد نظر بزرگ تر از حد مجاز است ({{filesize}}مگابایت). حد اکثر فایل مجاز: {{maxFilesize}}مگابایت."

      # If the file doesn't match the file type.
      dictInvalidFileType: "این نوع از فایل برای آپلود مجاز نیست."

      # If the server response was invalid.
      # `{{statusCode}}` will be replaced with the servers status code.
      dictResponseError: "سرور با کد {{statusCode}} پاسخ داد."

      # If `addRemoveLinks` is true, the text to be used for the cancel upload link.
      dictCancelUpload: "لغو آپلود"

      # If `addRemoveLinks` is true, the text to be used for confirmation when cancelling upload.
      dictCancelUploadConfirmation: "آیا از لغو آپلود مطمئن هستید ?"

      # If `addRemoveLinks` is true, the text to be used to remove a file.
      dictRemoveFile: "حذف فایل"

      # If this is not null, then the user will be prompted before removing a file.
      dictRemoveFileConfirmation: 'آیا از حذف این فایل اطمینان دارید ؟'

      # Displayed if `maxFiles` is st and exceeded.
      # The string `{{maxFiles}}` will be replaced by the configuration value.
      dictMaxFilesExceeded: "حد اکثر تعداد فایل مجاز آپلود استفاده شده است."

      # Allows you to translate the different units. Starting with `tb` for terabytes and going down to
      # `b` for bytes.
      dictFileSizeUnits: { tb: "ترابایت", gb: "گیگابایت", mb: "مگابایت", kb: "کیلوبایت", b: "بایت" }

      dictFileExists : "این فایل قبلا اضافه شده است."

      # Called when dropzone initialized
      # You can add event listeners here
      init: -> noop

      # A function that gets a [file](https://developer.mozilla.org/en-US/docs/DOM/File)
      # and a `done` function as parameters.
      #
      # If the done function is invoked without arguments, the file is "accepted" and will
      # be processed. If you pass an error message, the file is rejected, and the error
      # message will be displayed.
      # This function will not be called if the file is too big or doesn't match the mime types.
      accept: (file, done) -> done()


      # Gets called when the browser is not supported.
      # The default implementation shows the fallback input field and adds
      # a text.
      fallback: ->
        # This code should pass in IE7... :(
        @element.className = "#{@element.className} dz-browser-not-supported"

        for child in @element.getElementsByTagName "div"
          if /(^| )dz-message($| )/.test child.className
            messageElement = child
            child.className = "dz-message" # Removes the 'dz-default' class
            continue
        unless messageElement
          messageElement = Dropzone.createElement """<div class="dz-message"><span></span></div>"""
          @element.appendChild messageElement

        span = messageElement.getElementsByTagName("span")[0]
        if span
          if span.textContent?
            span.textContent = @options.dictFallbackMessage
          else if span.innerText?
            span.innerText = @options.dictFallbackMessage

        @element.appendChild @getFallbackForm()



      # Gets called to calculate the thumbnail dimensions.
      #
      # It gets `file`, `width` and `height` (both may be `null`) as parameters and must return an object containing:
      #
      #  - `srcWidth` & `srcHeight` (required)
      #  - `trgWidth` & `trgHeight` (required)
      #  - `srcX` & `srcY` (optional, default `0`)
      #  - `trgX` & `trgY` (optional, default `0`)
      #
      # Those values are going to be used by `ctx.drawImage()`.
      resize: (file, width, height, resizeMethod) ->
        info =
          srcX: 0
          srcY: 0
          srcWidth: file.width
          srcHeight: file.height

        srcRatio = file.width / file.height

        # Automatically calculate dimensions if not specified
        if !width? and !height?
          width = info.srcWidth
          height = info.srcHeight
        else if !width?
          width = height * srcRatio
        else if !height?
          height = width / srcRatio

        # Make sure images aren't upscaled
        width = Math.min width, info.srcWidth
        height = Math.min height, info.srcHeight

        trgRatio = width / height

        if info.srcWidth > width or info.srcHeight > height
          # Image is bigger and needs rescaling
          if resizeMethod == 'crop'
            if srcRatio > trgRatio
              info.srcHeight = file.height
              info.srcWidth = info.srcHeight * trgRatio
            else
              info.srcWidth = file.width
              info.srcHeight = info.srcWidth / trgRatio
          else if resizeMethod == 'contain'
            # Method 'contain'
            if srcRatio > trgRatio
              height = width / srcRatio
            else
              width = height * srcRatio
          else
            throw new Error "Unknown resizeMethod '#{resizeMethod}'"

        info.srcX = (file.width - info.srcWidth) / 2
        info.srcY = (file.height - info.srcHeight) / 2

        info.trgWidth = width
        info.trgHeight = height

        return info

      # Can be used to transform the file (for example, resize an image if necessary).
      #
      # The default implementation uses `resizeWidth` and `resizeHeight` (if provided) and resizes
      # images according to those dimensions.
      #
      # Gets the `file` as the first parameter, and a `done()` function as the second, that needs
      # to be invoked with the file when the transformation is done.
      transformFile: (file, done) ->
        if (@options.resizeWidth || @options.resizeHeight) and file.type.match(/image.*/)
          @resizeImage file, @options.resizeWidth, @options.resizeHeight, @options.resizeMethod, done
        else
          done file


      # A string that contains the template used for each dropped
      # file. Change it to fulfill your needs but make sure to properly
      # provide all elements.
      #
      # If you want to use an actual HTML element instead of providing a String
      # as a config option, you could create a div with the id `tpl`,
      # put the template inside it and provide the element like this:
      #
      #     document
      #       .querySelector('#tpl')
      #       .innerHTML
      #
      previewTemplate:  """
                        <div class="dz-preview dz-file-preview">
                          <div class="dz-actions">
                            <span class="is-main-item" data-dz-set-main></span>
                            <span class="is-secondary-item" data-dz-set-secondary></span>
                            <span class="remove-item" data-dz-remove></span>
                            <span class="add-caption" data-dz-add-cation></span>
                          </div>
                          <div class="dz-image"><img data-dz-thumbnail /></div>
                          <div class="dz-details">
                            <div class="dz-size"><span data-dz-size></span></div>
                            <div class="dz-filename"><span data-dz-name></span></div>
                          </div>
                          <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
                          <div class="dz-error-message"><span data-dz-errormessage></span></div>
                          <div class="dz-success-mark">
                            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                              <title>Check</title>
                              <defs></defs>
                              <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
                              </g>
                            </svg>
                          </div>
                          <div class="dz-error-mark">
                            <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
                              <title>Error</title>
                              <defs></defs>
                              <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
                                <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">
                                  <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>
                                </g>
                              </g>
                            </svg>
                          </div>
                        </div>
                        """

      # END OPTIONS
      # (Required by the dropzone documentation parser)


      ###
      Those functions register themselves to the events on init and handle all
      the user interface specific stuff. Overwriting them won't break the upload
      but can break the way it's displayed.
      You can overwrite them if you don't like the default behavior. If you just
      want to add an additional event handler, register it on the dropzone object
      and don't overwrite those options.
      ###




      # Those are self explanatory and simply concern the DragnDrop.
      drop: (e) -> @element.classList.remove "dz-drag-hover"
      dragstart: noop
      dragend: (e) -> @element.classList.remove "dz-drag-hover"
      dragenter: (e) -> @element.classList.add "dz-drag-hover"
      dragover: (e) -> @element.classList.add "dz-drag-hover"
      dragleave: (e) -> @element.classList.remove "dz-drag-hover"

      paste: noop

      # Called whenever there are no files left in the dropzone anymore, and the
      # dropzone should be displayed as if in the initial state.
      reset: ->
        @element.classList.remove "dz-started"

      # Called when a file is added to the queue
      # Receives `file`
      addedfile: (file) ->
        @element.classList.add "dz-started" if @element == @previewsContainer

        if @previewsContainer
          file.previewElement = Dropzone.createElement @options.previewTemplate.trim()
          file.previewTemplate = file.previewElement # Backwards compatibility
          @previewsContainer.appendChild file.previewElement
          node.textContent = file.name for node in file.previewElement.querySelectorAll("[data-dz-name]")
          node.innerHTML = @filesize file.size for node in file.previewElement.querySelectorAll("[data-dz-size]")

          if @options.addRemoveLinks
            file._removeLink = Dropzone.createElement """<a class="dz-remove" href="javascript:undefined;" data-dz-remove>#{@options.dictRemoveFile}</a>"""
            file.previewElement.appendChild file._removeLink

          removeFileEvent = (e) =>
            e.preventDefault()
            e.stopPropagation()
            if file.status == Dropzone.UPLOADING
              Dropzone.confirm @options.dictCancelUploadConfirmation, => @removeFile file
            else
              if @options.dictRemoveFileConfirmation
                Dropzone.confirm @options.dictRemoveFileConfirmation, => @removeFile file
              else
                @removeFile file

          addCaptionToFile = (e) =>
            e.preventDefault()
            e.stopPropagation()
            Dropzone.addCaptionToFile file
          setMainFile = (e) =>
            e.preventDefault()
            e.stopPropagation()
            Dropzone.setMainFile file

          setSecondaryFile = (e) =>
            e.preventDefault()
            e.stopPropagation()
            Dropzone.setSecondaryFile file

          removeLink.addEventListener "click", removeFileEvent for removeLink in file.previewElement.querySelectorAll("[data-dz-remove]")
          addCaptionLink.addEventListener "click", addCaptionToFile for addCaptionLink in file.previewElement.querySelectorAll("[data-dz-add-cation]")
          setMainFileLink.addEventListener "click", setMainFile for setMainFileLink in file.previewElement.querySelectorAll("[data-dz-set-main]")
          setSecondaryFileLink.addEventListener "click", setSecondaryFile for setSecondaryFileLink in file.previewElement.querySelectorAll("[data-dz-set-secondary]")


      # Called whenever a file is removed.
      removedfile: (file) ->
        file.previewElement?.parentNode.removeChild file.previewElement if file.previewElement
        @_updateMaxFilesReachedClass()

      # Called when a thumbnail has been generated
      # Receives `file` and `dataUrl`
      thumbnail: (file, dataUrl) ->
        if file.previewElement
          file.previewElement.classList.remove "dz-file-preview"
          for thumbnailElement in file.previewElement.querySelectorAll("[data-dz-thumbnail]")
            thumbnailElement.alt = file.name
            thumbnailElement.src = dataUrl

          setTimeout (=> file.previewElement.classList.add "dz-image-preview"), 1

      # Called whenever an error occurs
      # Receives `file` and `message`
      error: (file, message) ->
        if file.previewElement
          file.previewElement.classList.add "dz-error"
          message = message.error if typeof message != "String" and message.error
          node.textContent = message for node in file.previewElement.querySelectorAll("[data-dz-errormessage]")

      errormultiple: noop

      # Called when a file gets processed. Since there is a cue, not all added
      # files are processed immediately.
      # Receives `file`
      processing: (file) ->
        if file.previewElement
          file.previewElement.classList.add "dz-processing"
          file._removeLink.textContent = @options.dictCancelUpload if file._removeLink

      processingmultiple: noop

      # Called whenever the upload progress gets updated.
      # Receives `file`, `progress` (percentage 0-100) and `bytesSent`.
      # To get the total number of bytes of the file, use `file.size`
      uploadprogress: (file, progress, bytesSent) ->
        if file.previewElement
          for node in file.previewElement.querySelectorAll("[data-dz-uploadprogress]")
            if node.nodeName is 'PROGRESS'
              node.value = progress
            else
              node.style.width = "#{progress}%"

      # Called whenever the total upload progress gets updated.
      # Called with totalUploadProgress (0-100), totalBytes and totalBytesSent
      totaluploadprogress: noop

      # Called just before the file is sent. Gets the `xhr` object as second
      # parameter, so you can modify it (for example to add a CSRF token) and a
      # `formData` object to add additional information.
      sending: noop

      sendingmultiple: noop

      # When the complete upload is finished and successful
      # Receives `file`
      success: (file) ->
        file.previewElement.classList.add "dz-success" if file.previewElement

      successmultiple: noop

      # When the upload is canceled.
      canceled: (file) -> @emit "error", file, "Upload canceled."

      canceledmultiple: noop

      # When the upload is finished, either with success or an error.
      # Receives `file`
      complete: (file) ->
        file._removeLink.textContent = @options.dictRemoveFile if file._removeLink
        file.previewElement.classList.add "dz-complete" if file.previewElement

      completemultiple: noop

      maxfilesexceeded: noop

      maxfilesreached: noop

      queuecomplete: noop

      addedfiles: noop

    # global utility
    extend = (target, objects...) ->
      for object in objects
        target[key] = val for key, val of object
      target

    constructor: (@element, options) ->
      # For backwards compatibility since the version was in the prototype previously
      @version = Dropzone.version

      @defaultOptions.previewTemplate = @defaultOptions.previewTemplate.replace /\n*/g, ""

      @clickableElements = [ ]
      @listeners = [ ]
      @files = [] # All files

      @element = document.querySelector @element if typeof @element == "string"

      # Not checking if instance of HTMLElement or Element since IE9 is extremely weird.
      throw new Error "Invalid dropzone element." unless @element and @element.nodeType?

      throw new Error "Dropzone already attached." if @element.dropzone

      # Now add this dropzone to the instances.
      Dropzone.instances.push @

      # Put the dropzone inside the element itself.
      @element.dropzone = @

      elementOptions = Dropzone.optionsForElement(@element) ? { }

      @options = extend { }, @defaultOptions, elementOptions, options ? { }

      # If the browser failed, just call the fallback and leave
      return @options.fallback.call this if @options.forceFallback or !Dropzone.isBrowserSupported()

      # @options.url = @element.getAttribute "action" unless @options.url?
      @options.url = @element.getAttribute "action" unless @options.url?

      throw new Error "No URL provided." unless @options.url

      throw new Error "You can't provide both 'acceptedFiles' and 'acceptedMimeTypes'. 'acceptedMimeTypes' is deprecated." if @options.acceptedFiles and @options.acceptedMimeTypes

      # Backwards compatibility
      if @options.acceptedMimeTypes
        @options.acceptedFiles = @options.acceptedMimeTypes
        delete @options.acceptedMimeTypes

      # Backwards compatibility
      if @options.renameFilename?
        @options.renameFile = (file) => @options.renameFilename.call this, file.name, file

      @options.method = @options.method.toUpperCase()

      if (fallback = @getExistingFallback()) and fallback.parentNode
        # Remove the fallback
        fallback.parentNode.removeChild fallback

      # Display previews in the previewsContainer element or the Dropzone element unless explicitly set to false
      if @options.previewsContainer != false
        if @options.previewsContainer
          @previewsContainer = Dropzone.getElement @options.previewsContainer, "previewsContainer"
        else
          @previewsContainer = @element

      if @options.clickable
        if @options.clickable == yes
          @clickableElements = [ @element ]
        else
          @clickableElements = Dropzone.getElements @options.clickable, "clickable"


      @init()


    # Returns all files that have been accepted
    getAcceptedFiles: -> file for file in @files when file.accepted

    # Returns all files that have been rejected
    # Not sure when that's going to be useful, but added for completeness.
    getRejectedFiles: -> file for file in @files when not file.accepted

    getFilesWithStatus: (status) -> file for file in @files when file.status == status

    # Returns all files that are in the queue
    getQueuedFiles: -> @getFilesWithStatus Dropzone.QUEUED

    getUploadingFiles: -> @getFilesWithStatus Dropzone.UPLOADING

    getAddedFiles: -> @getFilesWithStatus Dropzone.ADDED

    # Files that are either queued or uploading
    getActiveFiles: -> file for file in @files when file.status == Dropzone.UPLOADING or file.status == Dropzone.QUEUED

    # The function that gets called when Dropzone is initialized. You
    # can (and should) setup event listeners inside this function.
    init: ->
      # In case it isn't set already
      @element.setAttribute("enctype", "multipart/form-data") if @element.tagName == "form"

      if @element.classList.contains("dropzone") and !@element.querySelector(".dz-message")
        @element.appendChild Dropzone.createElement """<div class="dz-default dz-message"><span>#{@options.dictDefaultMessage}</span></div>"""

      if @clickableElements.length
        setupHiddenFileInput = =>
          @hiddenFileInput.parentNode.removeChild @hiddenFileInput if @hiddenFileInput
          @hiddenFileInput = document.createElement "input"
          @hiddenFileInput.setAttribute "type", "file"
          @hiddenFileInput.setAttribute "multiple", "multiple" if !@options.maxFiles? || @options.maxFiles > 1
          @hiddenFileInput.className = "dz-hidden-input"

          @hiddenFileInput.setAttribute "accept", @options.acceptedFiles if @options.acceptedFiles?
          @hiddenFileInput.setAttribute "capture", @options.capture if @options.capture?

          # Not setting `display="none"` because some browsers don't accept clicks
          # on elements that aren't displayed.
          @hiddenFileInput.style.visibility = "hidden"
          @hiddenFileInput.style.position = "absolute"
          @hiddenFileInput.style.top = "0"
          @hiddenFileInput.style.left = "0"
          @hiddenFileInput.style.height = "0"
          @hiddenFileInput.style.width = "0"
          document.querySelector(@options.hiddenInputContainer).appendChild @hiddenFileInput
          @hiddenFileInput.addEventListener "change", =>
            files = @hiddenFileInput.files
            @addFile file for file in files if files.length
            @emit "addedfiles", files
            setupHiddenFileInput()
        setupHiddenFileInput()

      @URL = window.URL ? window.webkitURL


      # Setup all event listeners on the Dropzone object itself.
      # They're not in @setupEventListeners() because they shouldn't be removed
      # again when the dropzone gets disabled.
      @on eventName, @options[eventName] for eventName in @events

      @on "uploadprogress", => @updateTotalUploadProgress()

      @on "removedfile", => @updateTotalUploadProgress()

      @on "canceled", (file) => @emit "complete", file

      # Emit a `queuecomplete` event if all files finished uploading.
      @on "complete", (file) =>
        if @getAddedFiles().length == 0 and @getUploadingFiles().length == 0 and @getQueuedFiles().length == 0
          # This needs to be deferred so that `queuecomplete` really triggers after `complete`
          setTimeout (=> @emit "queuecomplete"), 0


      noPropagation = (e) ->
        e.stopPropagation()
        if e.preventDefault
          e.preventDefault()
        else
          e.returnValue = false

      # Create the listeners
      @listeners = [
        {
          element: @element
          events:
            "dragstart": (e) =>
              @emit "dragstart", e
            "dragenter": (e) =>
              noPropagation e
              @emit "dragenter", e
            "dragover": (e) =>
              # Makes it possible to drag files from chrome's download bar
              # http://stackoverflow.com/questions/19526430/drag-and-drop-file-uploads-from-chrome-downloads-bar
              # Try is required to prevent bug in Internet Explorer 11 (SCRIPT65535 exception)
              try efct = e.dataTransfer.effectAllowed
              e.dataTransfer.dropEffect = if 'move' == efct or 'linkMove' == efct then 'move' else 'copy'

              noPropagation e
              @emit "dragover", e
            "dragleave": (e) =>
              @emit "dragleave", e
            "drop": (e) =>
              noPropagation e
              @drop e
            "dragend": (e) =>
              @emit "dragend", e

            # This is disabled right now, because the browsers don't implement it properly.
            # "paste": (e) =>
            #   noPropagation e
            #   @paste e
        }
      ]

      @clickableElements.forEach (clickableElement) =>
        @listeners.push
          element: clickableElement
          events:
            "click": (evt) =>
              # Only the actual dropzone or the message element should trigger file selection
              if (clickableElement != @element) or (evt.target == @element or Dropzone.elementInside evt.target, @element.querySelector ".dz-message")
                @hiddenFileInput.click() # Forward the click
              return true

      @enable()

      @options.init.call @

    # Not fully tested yet
    destroy: ->
      @disable()
      @removeAllFiles true
      if @hiddenFileInput?.parentNode
        @hiddenFileInput.parentNode.removeChild @hiddenFileInput
        @hiddenFileInput = null
      delete @element.dropzone
      Dropzone.instances.splice Dropzone.instances.indexOf(this), 1


    updateTotalUploadProgress: ->
      totalBytesSent = 0
      totalBytes = 0

      activeFiles = @getActiveFiles()

      if activeFiles.length
        for file in @getActiveFiles()
          totalBytesSent += file.upload.bytesSent
          totalBytes += file.upload.total
        totalUploadProgress = 100 * totalBytesSent / totalBytes
      else
        totalUploadProgress = 100

      @emit "totaluploadprogress", totalUploadProgress, totalBytes, totalBytesSent

    # @options.paramName can be a function taking one parameter rather than a string.
    # A parameter name for a file is obtained simply by calling this with an index number.
    _getParamName: (n) ->
      if typeof @options.paramName is "function"
        @options.paramName n
      else
        "#{@options.paramName}#{if @options.uploadMultiple then "[#{n}]" else ""}"

    # If @options.renameFile is a function,
    # the function will be used to rename the file.name before appending it to the formData
    _renameFile: (file) ->
      return file.name unless typeof @options.renameFile is "function"
      @options.renameFile file

    # Returns a form that can be used as fallback if the browser does not support DragnDrop
    #
    # If the dropzone is already a form, only the input field and button are returned. Otherwise a complete form element is provided.
    # This code has to pass in IE7 :(
    getFallbackForm: ->
      return existingFallback if existingFallback = @getExistingFallback()

      fieldsString = """<div class="dz-fallback">"""
      fieldsString += """<p>#{@options.dictFallbackText}</p>""" if @options.dictFallbackText
      fieldsString += """<input type="file" name="#{@_getParamName 0}" #{if @options.uploadMultiple then 'multiple="multiple"' } /><input type="submit" value="Upload!"></div>"""

      fields = Dropzone.createElement fieldsString
      if @element.tagName isnt "FORM"
        form = Dropzone.createElement("""<form action="#{@options.url}" enctype="multipart/form-data" method="#{@options.method}"></form>""")
        form.appendChild fields
      else
        # Make sure that the enctype and method attributes are set properly
        @element.setAttribute "enctype", "multipart/form-data"
        @element.setAttribute "method", @options.method
      form ? fields


    # Returns the fallback elements if they exist already
    #
    # This code has to pass in IE7 :(
    getExistingFallback: ->
      getFallback = (elements) -> return el for el in elements when /(^| )fallback($| )/.test el.className

      for tagName in [ "div", "form" ]
        return fallback if fallback = getFallback @element.getElementsByTagName tagName


    # Activates all listeners stored in @listeners
    setupEventListeners: ->
      for elementListeners in @listeners
        elementListeners.element.addEventListener event, listener, false for event, listener of elementListeners.events


    # Deactivates all listeners stored in @listeners
    removeEventListeners: ->
      for elementListeners in @listeners
        elementListeners.element.removeEventListener event, listener, false for event, listener of elementListeners.events

    # Removes all event listeners and cancels all files in the queue or being processed.
    disable: ->
      @clickableElements.forEach (element) -> element.classList.remove "dz-clickable"
      @removeEventListeners()

      @cancelUpload file for file in @files

    enable: ->
      @clickableElements.forEach (element) -> element.classList.add "dz-clickable"
      @setupEventListeners()

    # Returns a nicely formatted filesize
    filesize: (size) ->
      selectedSize = 0
      selectedUnit = "b"

      if size > 0
        units = [ 'tb', 'gb', 'mb', 'kb', 'b' ]

        for unit, i in units
          cutoff = Math.pow(@options.filesizeBase, 4 - i) / 10

          if size >= cutoff
            selectedSize = size / Math.pow(@options.filesizeBase, 4 - i)
            selectedUnit = unit
            break

        selectedSize = Math.round(10 * selectedSize) / 10 # Cutting of digits

      "<strong>#{tools.convertNumberToPersian(selectedSize.toString())}</strong> #{@options.dictFileSizeUnits[selectedUnit]}"


    # Adds or removes the `dz-max-files-reached` class from the form.
    _updateMaxFilesReachedClass: ->
      if @options.maxFiles? and @getAcceptedFiles().length >= @options.maxFiles
        @emit 'maxfilesreached', @files if @getAcceptedFiles().length == @options.maxFiles
        @element.classList.add "dz-max-files-reached"
      else
        @element.classList.remove "dz-max-files-reached"



    drop: (e) ->
      return unless e.dataTransfer
      @emit "drop", e

      files = e.dataTransfer.files
      @emit "addedfiles", files

      # Even if it's a folder, files.length will contain the folders.
      if files.length
        items = e.dataTransfer.items
        if items and items.length and (items[0].webkitGetAsEntry?)
          # The browser supports dropping of folders, so handle items instead of files
          @_addFilesFromItems items
        else
          @handleFiles files
      return

    paste: (e) ->
      return unless e?.clipboardData?.items?

      @emit "paste", e
      items = e.clipboardData.items

      @_addFilesFromItems items if items.length


    handleFiles: (files) ->
      @addFile file for file in files

    # When a folder is dropped (or files are pasted), items must be handled
    # instead of files.
    _addFilesFromItems: (items) ->
      for item in items
        if item.webkitGetAsEntry? and entry = item.webkitGetAsEntry()
          if entry.isFile
            @addFile item.getAsFile()
          else if entry.isDirectory
            # Append all files from that directory to files
            @_addFilesFromDirectory entry, entry.name
        else if item.getAsFile?
          if !item.kind? or item.kind == "file"
            @addFile item.getAsFile()


    # Goes through the directory, and adds each file it finds recursively
    _addFilesFromDirectory: (directory, path) ->
      dirReader = directory.createReader()

      errorHandler = (error) -> console?.log? error

      readEntries = () =>
        dirReader.readEntries (entries) =>
          if entries.length > 0
            for entry in entries
              if entry.isFile
                entry.file (file) =>
                  return if @options.ignoreHiddenFiles and file.name.substring(0, 1) is '.'
                  file.fullPath = "#{path}/#{file.name}"
                  @addFile file
              else if entry.isDirectory
                @_addFilesFromDirectory entry, "#{path}/#{entry.name}"

            # Recursively call readEntries() again, since browser only handle
            # the first 100 entries.
            # See: https://developer.mozilla.org/en-US/docs/Web/API/DirectoryReader#readEntries
            readEntries()
          return null
        , errorHandler

      readEntries()



    # If `done()` is called without argument the file is accepted
    # If you call it with an error message, the file is rejected
    # (This allows for asynchronous validation)
    #
    # This function checks the filesize, and if the file.type passes the
    # `acceptedFiles` check.
    accept: (file, done) ->
      if file.size > @options.maxFilesize * 1024 * 1024
        done @options.dictFileTooBig.replace("{{filesize}}", Math.round(file.size / 1024 / 10.24) / 100).replace("{{maxFilesize}}", @options.maxFilesize)
      else unless Dropzone.isValidFile file, @options.acceptedFiles
        done @options.dictInvalidFileType
      else if @options.maxFiles? and @getAcceptedFiles().length >= @options.maxFiles
        done @options.dictMaxFilesExceeded.replace "{{maxFiles}}", @options.maxFiles
        @emit "maxfilesexceeded", file
      else
        @options.accept.call this, file, done

    addFile: (file) ->
      fileNewName = @_renameFile file
      ###
      Arash Khajelou
      Added these lines to check the file is already added to the system or not
      ###
      if (_.findIndex @files, { name :  fileNewName}) != -1
        jQuery(@element).trigger 'message:danger', @options.dictFileExists
        return
      file.upload =
        progress: 0
        # FeatureConfig the total upload size to file.size for the beginning
        # It's actual different than the size to be transmitted.
        total: file.size
        bytesSent: 0
        filename: fileNewName
      @files.push file

      file.status = Dropzone.ADDED

      @emit "addedfile", file

      @_enqueueThumbnail file

      @accept file, (error) =>
        if error
          file.accepted = false
          @_errorProcessing [ file ], error # Will set the file.status
        else
          file.accepted = true
          @enqueueFile file if @options.autoQueue # Will set .accepted = true
        @_updateMaxFilesReachedClass()


    # Wrapper for enqueueFile
    enqueueFiles: (files) -> @enqueueFile file for file in files; null

    enqueueFile: (file) ->
      if file.status == Dropzone.ADDED and file.accepted == true
        file.status = Dropzone.QUEUED
        if @options.autoProcessQueue
          setTimeout (=> @processQueue()), 0 # Deferring the call
      else
        throw new Error "This file can't be queued because it has already been processed or was rejected."


    _thumbnailQueue: [ ]
    _processingThumbnail: no
    _enqueueThumbnail: (file) ->
      if @options.createImageThumbnails and file.type.match(/image.*/) and file.size <= @options.maxThumbnailFilesize * 1024 * 1024
        @_thumbnailQueue.push(file)
        setTimeout (=> @_processThumbnailQueue()), 0 # Deferring the call

    _processThumbnailQueue: ->
      return if @_processingThumbnail or @_thumbnailQueue.length == 0

      @_processingThumbnail = yes
      file = @_thumbnailQueue.shift()
      @createThumbnail file, @options.thumbnailWidth, @options.thumbnailHeight, @options.thumbnailMethod, true, (dataUrl) =>
        @emit "thumbnail", file, dataUrl
        @_processingThumbnail = no
        @_processThumbnailQueue()


    # Can be called by the user to remove a file
    removeFile: (file) ->
      @cancelUpload file if file.status == Dropzone.UPLOADING
      @files = without @files, file

      @emit "removedfile", file
      @emit "reset" if @files.length == 0

    # Removes all files that aren't currently processed from the list
    removeAllFiles: (cancelIfNecessary = off) ->
      # Create a copy of files since removeFile() changes the @files array.
      for file in @files.slice()
        @removeFile file if file.status != Dropzone.UPLOADING || cancelIfNecessary
      return null

    # Resizes an image before it gets sent to the server. This function is the default behavior of
    # `options.transformFile` if `resizeWidth` or `resizeHeight` are set. The callback is invoked with
    # the resized blob.
    resizeImage: (file, width, height, resizeMethod, callback) ->
      @createThumbnail file, width, height, resizeMethod, false, (dataUrl, canvas) =>
        if canvas == null
          # The image has not been resized
          callback file
        else
          resizeMimeType = @options.resizeMimeType
          resizeMimeType ?= file.type
          resizedDataURL = canvas.toDataURL resizeMimeType, @options.resizeQuality
          if resizeMimeType == 'image/jpeg' || resizeMimeType == 'image/jpg'
            # Now add the original EXIF information
            resizedDataURL = ExifRestore.restore file.dataURL, resizedDataURL
          callback Dropzone.dataURItoBlob resizedDataURL

    createThumbnail: (file, width, height, resizeMethod, fixOrientation, callback) ->
      fileReader = new FileReader

      fileReader.onload = =>

        file.dataURL = fileReader.result

        # Don't bother creating a thumbnail for SVG images since they're vector
        if file.type == "image/svg+xml"
          callback(fileReader.result) if callback?
          return

        @createThumbnailFromUrl file, width, height, resizeMethod, fixOrientation, callback

      fileReader.readAsDataURL file

    createThumbnailFromUrl: (file, width, height, resizeMethod, fixOrientation, callback, crossOrigin) ->
      # Not using `new Image` here because of a bug in latest Chrome versions.
      # See https://github.com/enyo/dropzone/pull/226
      img = document.createElement "img"

      img.crossOrigin = crossOrigin if crossOrigin

      img.onload = =>
        loadExif = (callback) -> callback 1
        if EXIF? and fixOrientation
          loadExif = (callback) ->
            EXIF.getData img, () ->
              callback EXIF.getTag this, 'Orientation'

        loadExif (orientation) =>
          file.width = img.width
          file.height = img.height

          resizeInfo = @options.resize.call @, file, width, height, resizeMethod

          canvas = document.createElement "canvas"
          ctx = canvas.getContext "2d"

          canvas.width = resizeInfo.trgWidth
          canvas.height = resizeInfo.trgHeight

          if orientation > 4
            canvas.width = resizeInfo.trgHeight
            canvas.height = resizeInfo.trgWidth

          switch orientation
            when 2
              # horizontal flip
              ctx.translate canvas.width, 0
              ctx.scale -1, 1
            when 3
              # 180° rotate left
              ctx.translate canvas.width, canvas.height
              ctx.rotate Math.PI
            when 4
              # vertical flip
              ctx.translate 0, canvas.height
              ctx.scale 1, -1
            when 5
              # vertical flip + 90 rotate right
              ctx.rotate 0.5 * Math.PI
              ctx.scale 1, -1
            when 6
              # 90° rotate right
              ctx.rotate 0.5 * Math.PI
              ctx.translate 0, -canvas.height
            when 7
              # horizontal flip + 90 rotate right
              ctx.rotate 0.5 * Math.PI
              ctx.translate canvas.width, -canvas.height
              ctx.scale -1, 1
            when 8
              # 90° rotate left
              ctx.rotate -0.5 * Math.PI
              ctx.translate -canvas.width, 0

          # This is a bugfix for iOS' scaling bug.
          drawImageIOSFix ctx, img, resizeInfo.srcX ? 0, resizeInfo.srcY ? 0, resizeInfo.srcWidth, resizeInfo.srcHeight, resizeInfo.trgX ? 0, resizeInfo.trgY ? 0, resizeInfo.trgWidth, resizeInfo.trgHeight

          thumbnail = canvas.toDataURL "image/png"

          callback(thumbnail, canvas) if callback?

      img.onerror = callback if callback?

      img.src = file.dataURL


    # Goes through the queue and processes files if there aren't too many already.
    processQueue: ->
      parallelUploads = @options.parallelUploads
      processingLength = @getUploadingFiles().length
      i = processingLength

      # There are already at least as many files uploading than should be
      return if processingLength >= parallelUploads

      queuedFiles = @getQueuedFiles()

      return unless queuedFiles.length > 0

      if @options.uploadMultiple
        # The files should be uploaded in one request
        @processFiles queuedFiles.slice 0, (parallelUploads - processingLength)
      else
        while i < parallelUploads
          return unless queuedFiles.length # Nothing left to process
          @processFile queuedFiles.shift()
          i++


    # Wrapper for `processFiles`
    processFile: (file) -> @processFiles [ file ]


    # Loads the file, then calls finishedLoading()
    processFiles: (files) ->
      for file in files
        file.processing = yes # Backwards compatibility
        file.status = Dropzone.UPLOADING

        @emit "processing", file

      @emit "processingmultiple", files if @options.uploadMultiple

      @uploadFiles files



    _getFilesWithXhr: (xhr) -> files = (file for file in @files when file.xhr == xhr)


    # Cancels the file upload and sets the status to CANCELED
    # **if** the file is actually being uploaded.
    # If it's still in the queue, the file is being removed from it and the status
    # set to CANCELED.
    cancelUpload: (file) ->
      if file.status == Dropzone.UPLOADING
        groupedFiles = @_getFilesWithXhr file.xhr
        groupedFile.status = Dropzone.CANCELED for groupedFile in groupedFiles
        file.xhr.abort()
        @emit "canceled", groupedFile for groupedFile in groupedFiles
        @emit "canceledmultiple", groupedFiles if @options.uploadMultiple

      else if file.status in [ Dropzone.ADDED, Dropzone.QUEUED ]
        file.status = Dropzone.CANCELED
        @emit "canceled", file
        @emit "canceledmultiple", [ file ] if @options.uploadMultiple

      @processQueue() if @options.autoProcessQueue

    resolveOption = (option, args...) ->
      if typeof option == 'function'
        return option.apply(@, args)
      option

    # Wrapper for uploadFiles()
    uploadFile: (file) -> @uploadFiles [ file ]

    uploadFiles: (files) ->
      xhr = new XMLHttpRequest()

      # Put the xhr object in the file objects to be able to reference it later.
      file.xhr = xhr for file in files

      method = resolveOption @options.method, files
      url = resolveOption @options.url, files
      xhr.open method, url, true

      # FeatureConfig the timeout after open because of IE11 issue: https://gitlab.com/meno/dropzone/issues/8
      xhr.timeout = resolveOption @options.timeout, files

      # Has to be after `.open()`. See https://github.com/enyo/dropzone/issues/179
      xhr.withCredentials = !!@options.withCredentials


      response = null

      handleError = =>
        for file in files
          @_errorProcessing files, response || @options.dictResponseError.replace("{{statusCode}}", xhr.status), xhr


      updateProgress = (e) =>
        if e?
          progress = 100 * e.loaded / e.total

          for file in files
            file.upload.progress = progress
            file.upload.total = e.total
            file.upload.bytesSent = e.loaded
        else
          # Called when the file finished uploading

          allFilesFinished = yes

          progress = 100

          for file in files
            allFilesFinished = no unless file.upload.progress == 100 and file.upload.bytesSent == file.upload.total
            file.upload.progress = progress
            file.upload.bytesSent = file.upload.total

          # Nothing to do, all files already at 100%
          return if allFilesFinished

        for file in files
          @emit "uploadprogress", file, progress, file.upload.bytesSent

      xhr.onload = (e) =>
        return if files[0].status == Dropzone.CANCELED

        return unless xhr.readyState is 4

        if xhr.responseType != 'arraybuffer' && xhr.responseType != 'blob'
          response = xhr.responseText

          if xhr.getResponseHeader("content-type") and ~xhr.getResponseHeader("content-type").indexOf "application/json"
            try
              response = JSON.parse response
            catch e
              response = "Invalid JSON response from server."

        updateProgress()

        unless 200 <= xhr.status < 300
          handleError()
        else
          @_finished files, response, e

      xhr.onerror = =>
        return if files[0].status == Dropzone.CANCELED
        handleError()

      # Some browsers do not have the .upload property
      progressObj = xhr.upload ? xhr
      progressObj.onprogress = updateProgress

      headers =
        "Accept": "application/json",
        "Cache-Control": "no-cache",
        "X-Requested-With": "XMLHttpRequest",

      extend headers, @options.headers if @options.headers

      for headerName, headerValue of headers
        xhr.setRequestHeader headerName, headerValue if headerValue

      formData = new FormData()

      # Adding all @options parameters
      formData.append key, value for key, value of @options.params if @options.params
      jQuery(@element).find('ul.form-data li').each () ->
        parameter = jQuery(this)
        formData.append (parameter.attr('name') || 'null') , (parameter.attr('value') || 'null')

      # Let the user add additional data if necessary
      @emit "sending", file, xhr, formData for file in files
      @emit "sendingmultiple", files, xhr, formData if @options.uploadMultiple


      # Take care of other input elements
      if @element.tagName == "FORM"
        for input in @element.querySelectorAll "input, textarea, select, button"
          inputName = input.getAttribute "name"
          inputType = input.getAttribute "type"

          if input.tagName == "SELECT" and input.hasAttribute "multiple"
            # Possibly multiple values
            formData.append inputName, option.value for option in input.options when option.selected
          else if !inputType or (inputType.toLowerCase() not in [ "checkbox", "radio" ]) or input.checked
            formData.append inputName, input.value


      # Finally add the files
      # Has to be last because some servers (eg: S3) expect the file to be the last parameter

      # Clumsy way of handling asynchronous calls, until I get to add a proper Future library.
      doneCounter = 0

      for i in [0..files.length-1]
        doneFunction = (file, paramName, fileName) => (transformedFile) =>
          formData.append paramName, transformedFile, fileName
          @submitRequest xhr, formData, files if ++doneCounter == files.length

        @options.transformFile.call @, files[i], doneFunction(files[i], @_getParamName(i), files[i].upload.filename)


    submitRequest: (xhr, formData, files) ->
      xhr.send formData

    # Called internally when processing is finished.
    # Individual callbacks have to be called in the appropriate sections.
    _finished: (files, responseText, e) ->
      for file in files
        file.status = Dropzone.SUCCESS
        ###
        end of custom scripts
        ###
        @emit "success", file, responseText, e
        @emit "complete", file
      if @options.uploadMultiple
        @emit "successmultiple", files, responseText, e
        @emit "completemultiple", files

      @processQueue() if @options.autoProcessQueue

    # Called internally when processing is finished.
    # Individual callbacks have to be called in the appropriate sections.
    _errorProcessing: (files, message, xhr) ->
      if ( typeof message.transmission != "undefined" && typeof message.transmission.messages != "undefined" and
      message.transmission.messages.length > 0 )
        jQuery(@element).trigger 'message:danger', message.transmission.messages[0]
      for file in files
        file.status = Dropzone.ERROR
        @emit "error", file, message, xhr
        @emit "complete", file
      if @options.uploadMultiple
        @emit "errormultiple", files, message, xhr
        @emit "completemultiple", files

      @processQueue() if @options.autoProcessQueue



  Dropzone.version = "5.1.1"


  # This is a map of options for your different dropzones. Add configurations
  # to this object for your different dropzone elemens.
  #
  # Example:
  #
  #     Dropzone.options.myDropzoneElementId = { maxFilesize: 1 };
  #
  # To disable autoDiscover for a specific element, you can set `false` as an option:
  #
  #     Dropzone.options.myDisabledElementId = false;
  #
  # And in html:
  #
  #     <form action="/upload" id="my-dropzone-element-id" class="dropzone"></form>
  Dropzone.options = { }

  Dropzone.setMainFile = (file) =>
    console.log(file)
  Dropzone.setSecondaryFile = (file) =>
    console.log(file)
  Dropzone.addCaptionToFile = (file) =>
    console.log(file)

  # Returns the options for an element or undefined if none available.
  Dropzone.optionsForElement = (element) ->
    # Get the `Dropzone.options.elementId` for this element if it exists
    if element.getAttribute("id") then Dropzone.options[camelize element.getAttribute "id"] else undefined


  # Holds a list of all dropzone instances
  Dropzone.instances = [ ]

  # Returns the dropzone for given element if any
  Dropzone.forElement = (element) ->
    element = document.querySelector element if typeof element == "string"
    throw new Error "No Dropzone found for given element. This is probably because you're trying to access it before Dropzone had the time to initialize. Use the `init` option to setup any additional observers on your Dropzone." unless element?.dropzone?
    return element.dropzone


  # Set to false if you don't want Dropzone to automatically find and attach to .dropzone elements.
  Dropzone.autoDiscover = on

  # Looks for all .dropzone elements and creates a dropzone for them
  Dropzone.discover = ->
    if document.querySelectorAll
      dropzones = document.querySelectorAll ".dropzone"
    else
      dropzones = [ ]
      # IE :(
      checkElements = (elements) ->
        for el in elements
          dropzones.push el if /(^| )dropzone($| )/.test el.className
      checkElements document.getElementsByTagName "div"
      checkElements document.getElementsByTagName "form"

    for dropzone in dropzones
      # Create a dropzone unless auto discover has been disabled for specific element
      new Dropzone dropzone unless Dropzone.optionsForElement(dropzone) == false



  # Since the whole Drag'n'Drop API is pretty new, some browsers implement it,
  # but not correctly.
  # So I created a blacklist of userAgents. Yes, yes. Browser sniffing, I know.
  # But what to do when browsers *theoretically* support an API, but crash
  # when using it.
  #
  # This is a list of regular expressions tested against navigator.userAgent
  #
  # ** It should only be used on browser that *do* support the API, but
  # incorrectly **
  #
  Dropzone.blacklistedBrowsers = [
    # The mac os version of opera 12 seems to have a problem with the File drag'n'drop API.
    /opera.*Macintosh.*version\/12/i
    # /MSIE\ 10/i
  ]


  # Checks if the browser is supported
  Dropzone.isBrowserSupported = ->
    capableBrowser = yes

    if window.File and window.FileReader and window.FileList and window.Blob and window.FormData and document.querySelector
      unless "classList" of document.createElement "a"
        capableBrowser = no
      else
        # The browser supports the API, but may be blacklisted.
        for regex in Dropzone.blacklistedBrowsers
          if regex.test navigator.userAgent
            capableBrowser = no
            continue
    else
      capableBrowser = no

    capableBrowser

  Dropzone.dataURItoBlob = (dataURI) ->
    # convert base64 to raw binary data held in a string
    # doesn't handle URLEncoded DataURIs - see SO answer #6850276 for code that does this
    byteString = atob dataURI.split(',')[1]

    # separate out the mime component
    mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]

    # write the bytes of the string to an ArrayBuffer
    ab = new ArrayBuffer byteString.length
    ia = new Uint8Array ab
    for i in [0 .. byteString.length]
      ia[i] = byteString.charCodeAt i

    # write the ArrayBuffer to a blob
    new Blob [ab], {type: mimeString}

  # Returns an array without the rejected item
  without = (list, rejectedItem) -> item for item in list when item isnt rejectedItem

  # abc-def_ghi -> abcDefGhi
  camelize = (str) -> str.replace /[\-_](\w)/g, (match) -> match.charAt(1).toUpperCase()

  # Creates an element from string
  Dropzone.createElement = (string) ->
    div = document.createElement "div"
    div.innerHTML = string
    div.childNodes[0]

  # Tests if given element is inside (or simply is) the container
  Dropzone.elementInside = (element, container) ->
    return yes if element == container # Coffeescript doesn't support do/while loops
    return yes while element = element.parentNode when element == container
    return no



  Dropzone.getElement = (el, name) ->
    if typeof el == "string"
      element = document.querySelector el
    else if el.nodeType?
      element = el
    throw new Error "Invalid `#{name}` option provided. Please provide a CSS selector or a plain HTML element." unless element?
    return element


  Dropzone.getElements = (els, name) ->
    if els instanceof Array
      elements = [ ]
      try
        elements.push @getElement el, name for el in els
      catch e
        elements = null
    else if typeof els == "string"
      elements = [ ]
      elements.push el for el in document.querySelectorAll els
    else if els.nodeType?
      elements = [ els ]

    throw new Error "Invalid `#{name}` option provided. Please provide a CSS selector, a plain HTML element or a list of those." unless elements? and elements.length

    return elements

  # Asks the user the question and calls accepted or rejected accordingly
  #
  # The default implementation just uses `window.confirm` and then calls the
  # appropriate callback.

  #Dropzone.confirm = (question, accepted, rejected) ->
  #  if window.confirm question
  #    accepted()
  #  else if rejected?
  #    rejected()

  Dropzone.confirm = (question, accepted, rejected) ->
    window.customConfirm question, accepted, rejected

  # Validates the mime type like this:
  #
  # https://developer.mozilla.org/en-US/docs/HTML/Element/input#attr-accept
  Dropzone.isValidFile = (file, acceptedFiles) ->
    return yes unless acceptedFiles # If there are no accepted mime types, it's OK
    acceptedFiles = acceptedFiles.split ","

    mimeType = file.type
    baseMimeType = mimeType.replace /\/.*$/, ""

    for validType in acceptedFiles
      validType = validType.trim()
      if validType.charAt(0) == "."
        return yes if file.name.toLowerCase().indexOf(validType.toLowerCase(), file.name.length - validType.length) != -1
      else if /\/\*$/.test validType
        # This is something like a image/* mime type
        return yes if baseMimeType == validType.replace /\/.*$/, ""
      else
        return yes if mimeType == validType

    return no


  # Augment jQuery
  if jQuery?
    jQuery.fn.dropzone = (options) ->
      this.each -> new Dropzone this, options




  if module?
    module.exports = Dropzone
  else
    window.Dropzone = Dropzone





  # Dropzone file status codes
  Dropzone.ADDED = "added"

  Dropzone.QUEUED = "queued"
  # For backwards compatibility. Now, if a file is accepted, it's either queued
  # or uploading.
  Dropzone.ACCEPTED = Dropzone.QUEUED

  Dropzone.UPLOADING = "uploading"
  Dropzone.PROCESSING = Dropzone.UPLOADING # alias

  Dropzone.CANCELED = "canceled"
  Dropzone.ERROR = "error"
  Dropzone.SUCCESS = "success"





  ###

  Bugfix for iOS 6 and 7
  Source: http://stackoverflow.com/questions/11929099/html5-canvas-drawimage-ratio-bug-ios
  based on the work of https://github.com/stomita/ios-imagefile-megapixel

  ###

  # Detecting vertical squash in loaded image.
  # Fixes a bug which squash image vertically while drawing into canvas for some images.
  # This is a bug in iOS6 devices. This function from https://github.com/stomita/ios-imagefile-megapixel
  detectVerticalSquash = (img) ->
    iw = img.naturalWidth
    ih = img.naturalHeight
    canvas = document.createElement("canvas")
    canvas.width = 1
    canvas.height = ih
    ctx = canvas.getContext("2d")
    ctx.drawImage img, 0, 0
    data = ctx.getImageData(1, 0, 1, ih).data


    # search image edge pixel position in case it is squashed vertically.
    sy = 0
    ey = ih
    py = ih
    while py > sy
      alpha = data[(py - 1) * 4 + 3]

      if alpha is 0 then ey = py else sy = py

      py = (ey + sy) >> 1
    ratio = (py / ih)

    if (ratio is 0) then 1 else ratio

  # A replacement for context.drawImage
  # (args are for source and destination).
  drawImageIOSFix = (ctx, img, sx, sy, sw, sh, dx, dy, dw, dh) ->
    vertSquashRatio = detectVerticalSquash img
    ctx.drawImage img, sx, sy, sw, sh, dx, dy, dw, dh / vertSquashRatio





  # Based on MinifyJpeg
  # Source: http://www.perry.cz/files/ExifRestorer.js
  # http://elicon.blog57.fc2.com/blog-entry-206.html
  class ExifRestore
    @KEY_STR: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/='

    @encode64: (input) ->
      output = ''
      chr1 = undefined
      chr2 = undefined
      chr3 = ''
      enc1 = undefined
      enc2 = undefined
      enc3 = undefined
      enc4 = ''
      i = 0
      loop
        chr1 = input[i++]
        chr2 = input[i++]
        chr3 = input[i++]
        enc1 = chr1 >> 2
        enc2 = (chr1 & 3) << 4 | chr2 >> 4
        enc3 = (chr2 & 15) << 2 | chr3 >> 6
        enc4 = chr3 & 63
        if isNaN(chr2)
          enc3 = enc4 = 64
        else if isNaN(chr3)
          enc4 = 64
        output = output + @KEY_STR.charAt(enc1) + @KEY_STR.charAt(enc2) + @KEY_STR.charAt(enc3) + @KEY_STR.charAt(enc4)
        chr1 = chr2 = chr3 = ''
        enc1 = enc2 = enc3 = enc4 = ''
        unless i < input.length
          break
      output

    @restore: (origFileBase64, resizedFileBase64) ->
      if !origFileBase64.match('data:image/jpeg;base64,')
        return resizedFileBase64
      rawImage = @decode64(origFileBase64.replace('data:image/jpeg;base64,', ''))
      segments = @slice2Segments(rawImage)
      image = @exifManipulation(resizedFileBase64, segments)
      'data:image/jpeg;base64,' + @encode64 image

    @exifManipulation: (resizedFileBase64, segments) ->
      exifArray = @getExifArray(segments)
      newImageArray = @insertExif(resizedFileBase64, exifArray)
      aBuffer = new Uint8Array(newImageArray)
      aBuffer

    @getExifArray: (segments) ->
      seg = undefined
      x = 0
      while x < segments.length
        seg = segments[x]
        if seg[0] == 255 & seg[1] == 225
          return seg
        x++
      []

    @insertExif: (resizedFileBase64, exifArray) ->
      imageData = resizedFileBase64.replace('data:image/jpeg;base64,', '')
      buf = @decode64(imageData)
      separatePoint = buf.indexOf(255, 3)
      mae = buf.slice(0, separatePoint)
      ato = buf.slice(separatePoint)
      array = mae
      array = array.concat(exifArray)
      array = array.concat(ato)
      array

    @slice2Segments: (rawImageArray) ->
      head = 0
      segments = []
      loop
        if rawImageArray[head] == 255 & rawImageArray[head + 1] == 218
          break
        if rawImageArray[head] == 255 & rawImageArray[head + 1] == 216
          head += 2
        else
          length = rawImageArray[head + 2] * 256 + rawImageArray[head + 3]
          endPoint = head + length + 2
          seg = rawImageArray.slice(head, endPoint)
          segments.push seg
          head = endPoint
        if head > rawImageArray.length
          break
      segments

    @decode64: (input) ->
      output = ''
      chr1 = undefined
      chr2 = undefined
      chr3 = ''
      enc1 = undefined
      enc2 = undefined
      enc3 = undefined
      enc4 = ''
      i = 0
      buf = []
      # remove all characters that are not A-Z, a-z, 0-9, +, /, or =
      base64test = /[^A-Za-z0-9\+\/\=]/g
      if base64test.exec(input)
        console.warning 'There were invalid base64 characters in the input text.\n' + 'Valid base64 characters are A-Z, a-z, 0-9, \'+\', \'/\',and \'=\'\n' + 'Expect errors in decoding.'
      input = input.replace(/[^A-Za-z0-9\+\/\=]/g, '')
      loop
        enc1 = @KEY_STR.indexOf(input.charAt(i++))
        enc2 = @KEY_STR.indexOf(input.charAt(i++))
        enc3 = @KEY_STR.indexOf(input.charAt(i++))
        enc4 = @KEY_STR.indexOf(input.charAt(i++))
        chr1 = enc1 << 2 | enc2 >> 4
        chr2 = (enc2 & 15) << 4 | enc3 >> 2
        chr3 = (enc3 & 3) << 6 | enc4
        buf.push chr1
        if enc3 != 64
          buf.push chr2
        if enc4 != 64
          buf.push chr3
        chr1 = chr2 = chr3 = ''
        enc1 = enc2 = enc3 = enc4 = ''
        unless i < input.length
          break
      buf




  ###
  # contentloaded.js
  #
  # Author: Diego Perini (diego.perini at gmail.com)
  # Summary: cross-browser wrapper for DOMContentLoaded
  # Updated: 20101020
  # License: MIT
  # Version: 1.2
  #
  # URL:
  # http://javascript.nwbox.com/ContentLoaded/
  # http://javascript.nwbox.com/ContentLoaded/MIT-LICENSE
  ###

  # @win window reference
  # @fn function reference
  contentLoaded = (win, fn) ->
    done = false
    top = true
    doc = win.document
    root = doc.documentElement
    add = (if doc.addEventListener then "addEventListener" else "attachEvent")
    rem = (if doc.addEventListener then "removeEventListener" else "detachEvent")
    pre = (if doc.addEventListener then "" else "on")
    init = (e) ->
      return  if e.type is "readystatechange" and doc.readyState isnt "complete"
      ((if e.type is "load" then win else doc))[rem] pre + e.type, init, false
      fn.call win, e.type or e  if not done and (done = true)

    poll = ->
      try
        root.doScroll "left"
      catch e
        setTimeout poll, 50
        return
      init "poll"

    unless doc.readyState is "complete"
      if doc.createEventObject and root.doScroll
        try
          top = not win.frameElement
        poll()  if top
      doc[add] pre + "DOMContentLoaded", init, false
      doc[add] pre + "readystatechange", init, false
      win[add] pre + "load", init, false


  # As a single function to be able to write tests.
  Dropzone._autoDiscoverFunction = -> Dropzone.discover() if Dropzone.autoDiscover
  contentLoaded window, Dropzone._autoDiscoverFunction
