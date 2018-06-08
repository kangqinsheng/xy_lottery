/**
 * @filename mobile-uploadImg
 * @description
 * ����: 418239385(418239385@qq.com)
 * ����ʱ��: 2016-4-6 14:38:03
 * �޸ļ�¼:
 *
 **/
//------------------------------------------------------------------------------
function mobileUploadImg(opts)
{
    if(opts == null){
        objGlobal.DIC.dialog({content:'�������ϴ�ͼƬ����ز�����', autoClose:false, okValue:"ȷ��", isMask:true});
        return;
    }
    var objUploadImg = {};
    var opts = opts || {};
    //�ϴ�ͼƬ�ı�ID
    objUploadImg.formHtmlId = opts.formHtmlId || "";
    //����ϴ�ͼƬ��
    objUploadImg.maxUpload = opts.maxUpload || 8;
    //����ͼƬ�������
    objUploadImg.uploadMaxW = opts.uploadMaxW || 800;
    //����ͼƬ�����߶�
    objUploadImg.uploadMaxH = opts.uploadMaxH || 800;
    //Ŀ��jpgͼƬ�������
    objUploadImg.uploadQuality = opts.uploadQuality || 1;
    //�ϴ�����ͼƬ���(MB)   Ĭ��8M
    objUploadImg.uploadPicSize = opts.uploadPicSize || 8;
    //�Ƿ������ͼ�ϴ�  Ĭ�ϵ����ϴ�
    objUploadImg.uploadPicMore = opts.uploadPicMore || false;
    //��ͼ�ϴ�ʱ��һ���ϴ���������� Ĭ��10
    objUploadImg.onceMaxUpload = opts.onceMaxUpload || 10;
    // �ϴ�ͼƬ��ַ
    objUploadImg.uploadUrl = opts.uploadUrl || "";
    //ѹ��ͼƬʱ��Ĭ��ͼƬ��ַ
    objUploadImg.uploadDefaultImgUrl = opts.uploadDefaultImgUrl || "";
    //��ͼ�ϴ�ʱ���ϴ���һ�ŵ����һ�ŵ�״̬
    objUploadImg.isMoreBusy = false;
    // �ϴ���Ϣ ��Ҫ�� id ��Ӧ��Ϣ
    objUploadImg.uploadInfo = {};
    // �ϴ����У����汣����� id
    objUploadImg.uploadQueue = [];
    // Ԥ�����У����汣����� id
    objUploadImg.previewQueue = [];
    // �������
    objUploadImg.xhr = {};
    // �Ƿ���ͼƬ����ѹ��
    objUploadImg.isEncoderBusy = false;
    // �Ƿ������������ϴ�
    objUploadImg.isBusy = false;

    if(objUploadImg.formHtmlId.length <= 0){
        objGlobal.DIC.dialog({content:'�������ϴ�ͼƬ������ID��', autoClose:false, okValue:"ȷ��", isMask:true});
        return;
    }

    if(objUploadImg.uploadUrl.length <= 0){
        objGlobal.DIC.dialog({content:'�������ϴ�ͼƬ��URL��ַ��', autoClose:false, okValue:"ȷ��", isMask:true});
        return;
    }

    objUploadImg.countUpload = function() {
        var num = 0;
        $.each(objUploadImg.uploadInfo, function(i, n) {
            if (n) {
                ++ num;
            }
        });
        return num;
    };

    // ͼƬԤ��
    objUploadImg.uploadPreview = function(id) {
        var reader = new FileReader();

        var uploadBase64;
        var conf = {}, file = objUploadImg.uploadInfo[id].file;

        conf = {
            maxW: objUploadImg.uploadMaxW, //Ŀ���
            maxH: objUploadImg.uploadMaxH, //Ŀ���
            quality: objUploadImg.uploadQuality, //Ŀ��jpgͼƬ�������
        };

        reader.onload = function(e) {
            var result = this.result;

            // �����jpg��ʽͼƬ����ȡͼƬ���㷽��,�Զ���ת
            if (file.type == 'image/jpeg'){
                try {
                    var jpg = new objJpegMeata.JpegMeta.JpegFile(result, file.name);
                } catch (e) {
                    objGlobal.DIC.dialog({content:'ͼƬ������ȷ��ͼƬ����', autoClose:true});
                    $(objUploadImg.formHtmlId + ' #li' + id).remove();
                    objUploadImg.isEncoderBusy = false;
                    return false;
                }
                if (jpg.tiff && jpg.tiff.Orientation) {
                    //������ת
                    conf = $.extend(conf, {
                        orien: jpg.tiff.Orientation.value
                    });
                }
            }

            // ѹ��
            if (objImageCompresser.ImageCompresser.support()) {
                var img = new Image();
                img.onload = function() {
                    try {
                        uploadBase64 = objImageCompresser.ImageCompresser.getImageBase64(this, conf);
                    } catch (e) {
                        objGlobal.DIC.dialog({content:'ѹ��ͼƬʧ��', autoClose:true});
                        $(objUploadImg.formHtmlId + ' #li' + id).remove();
                        objUploadImg.isEncoderBusy = false;
                        return false;
                    }
                    if (uploadBase64.indexOf('data:image') < 0) {
                        objGlobal.DIC.dialog({content:'�ϴ���Ƭ��ʽ��֧��', autoClose:true});
                        $(objUploadImg.formHtmlId + ' #li' + id).remove();
                        objUploadImg.isEncoderBusy = false;
                        return false;
                    }

                    objUploadImg.uploadInfo[id].file = uploadBase64;
                    $(objUploadImg.formHtmlId + ' #li' + id).find('img').attr('src', uploadBase64);
                    objUploadImg.uploadQueue.push(id);
                }
                img.onerror = function() {
                    objGlobal.DIC.dialog({content:'����ͼƬ����ʧ��', autoClose:true});
                    $(objUploadImg.formHtmlId + ' #li' + id).remove();
                    objUploadImg.isEncoderBusy = false;
                    return false;
                }
                img.src = objImageCompresser.ImageCompresser.getFileObjectURL(file);
            } else {
                uploadBase64 = result;
                if (uploadBase64.indexOf('data:image') < 0) {
                    objGlobal.DIC.dialog({content:'�ϴ���Ƭ��ʽ��֧��', autoClose:true});
                    $(objUploadImg.formHtmlId + ' #li' + id).remove();
                    objUploadImg.isEncoderBusy = false;
                    return false;
                }

                objUploadImg.uploadInfo[id].file = uploadBase64;
                $(objUploadImg.formHtmlId + ' #li' + id).find('img').attr('src', uploadBase64);
                objUploadImg.uploadQueue.push(id);
            }
        }

        reader.readAsBinaryString(objUploadImg.uploadInfo[id].file);
    };

    // �����ϴ�����
    objUploadImg.createUpload = function(id, type, uploadTimer) {
        if (!objUploadImg.uploadInfo[id]) {
            objUploadImg.isEncoderBusy = false;
            objUploadImg.isBusy = false;
            return false;
        }
        // �Ƴ�ͼƬѹ����...
        $(objUploadImg.formHtmlId + ' #li' + id).find('.mbupload_maskTxt').remove();

        // ͼƬposturl
        var uploadUrl = objUploadImg.uploadUrl;
        // ����������
        var progressHtml = '<div class="mbupload_progress mbupload_brSmall" id="mbupload_progress'+id+'"><div class="mbupload_proBar" style="width:0%;"></div></div>';
        $(objUploadImg.formHtmlId + ' #li' + id).find('.mbupload_maskLay').after(progressHtml);

        var formData = new FormData();
        formData.append('upload_pic', objUploadImg.uploadInfo[id].file);
        formData.append('upload_name', objUploadImg.uploadInfo[id].oldFileInfo.name);
        formData.append('upload_id', id);

        var progress = function(e) {
            if (e.target.response) {
                var result = $.parseJSON(e.target.response);

                if (result.errCode != 0) {
                    // $('#content').val(result.errCode);
                    objGlobal.DIC.dialog({content:'���粻�ȶ������Ժ����²���', autoClose:true});
                    removePic(id);
                    //����ʣ���ϴ���
                    objUploadImg.uploadRemaining();
                    return false;
                }
            }

            var progress = $(objUploadImg.formHtmlId + ' #mbupload_progress' + id).find('.mbupload_proBar');
            if (e.total == e.loaded) {
                var percent = 100;
            } else {
                var percent = 100*(e.loaded / e.total);
            }

            // ���ƽ�������Ҫ����
            if (percent > 100) {
                percent = 100;
            }

            progress.width(percent + '%');
            //progress.animate({'width': '95%'}, 1500);

            setTimeout(function(){
                if (percent == 100) {
                    donePic(id);
                    var pLength = 0, nLength = 0;
                    if(objUploadImg.uploadPicMore){
                        pLength = objUploadImg.previewQueue.length;
                        nLength = objUploadImg.uploadQueue.length;
                    }
                    if(uploadTimer && pLength <= 0 && nLength <= 0){
                        clearInterval(uploadTimer);
                    }
                }
            }, 400);
        }

        var removePic = function(id) {
            donePic(id);
            $('#li' + id).remove();
        }

        var donePic = function(id) {
            objUploadImg.isEncoderBusy = false;
            objUploadImg.isBusy = false;
            if(objUploadImg.uploadPicMore && (objUploadImg.previewQueue.length <= 0) &&
                 (objUploadImg.uploadQueue.length <= 0)){
                objUploadImg.isMoreBusy = false;
            }
            if (typeof objUploadImg.uploadInfo[id] != 'undefined') {
                objUploadImg.uploadInfo[id].isDone = true;
            }
            if (typeof objUploadImg.xhr[id] != 'undefined') {
                objUploadImg.xhr[id] = null;
            }
        }

        var complete = function(e) {
            var progress = $(objUploadImg.formHtmlId + ' #mbupload_progress' + id).find('.mbupload_proBar');
            progress.css('width', '100%');
            if($(objUploadImg.formHtmlId + ' #li' + id)){
                $(objUploadImg.formHtmlId + ' #li' + id).find('.mbupload_maskTxt').remove();
            }
            $(objUploadImg.formHtmlId + ' #li' + id).find('.mbupload_maskLay').remove();
            $(objUploadImg.formHtmlId + ' #li' + id).find('.mbupload_progress').remove();
            // �ϴ�����
            donePic(id);

            var result = $.parseJSON(e.target.response);
            if (result.errCode == 0) {
                var input = '<input type="hidden" id="input' + result.data.id + '" name="jing_img[]" value="' + result.data.picId + '">';
                if(type == 'replyForm'){
                    $('#replyForm').append(input);
                }else{
                    $(objUploadImg.formHtmlId).append(input);
                }

            } else {
                objGlobal.DIC.dialog({content:'���粻�ȶ������Ժ����²���', autoClose:true});
                removePic(id);
                //����ʣ���ϴ���
                objUploadImg.uploadRemaining();
                delete objUploadImg.uploadInfo[id];
                // �������ʧ�ܣ��ϴ�����������������������ʾ�Ӻ�
                if (objUploadImg.countUpload() < objUploadImg.maxUpload) {
                    $(objUploadImg.formHtmlId + ' .mbupload_addPic').show();
                }
            }
        }

        var failed = function() {
            objGlobal.DIC.dialog({content:'����Ͽ������Ժ����²���', autoClose:true});
            removePic(id)
        }

        var abort = function() {
            objGlobal.DIC.dialog({content:'�ϴ���ȡ��', autoClose:true});
            removePic(id)
        }

        // ���� ajax ����
        objUploadImg.xhr[id] = new XMLHttpRequest();
        objUploadImg.xhr[id].addEventListener("progress", progress, false);
        objUploadImg.xhr[id].upload.addEventListener("progress", progress, false);
        objUploadImg.xhr[id].addEventListener("load", complete, false);
        objUploadImg.xhr[id].addEventListener("abort", abort, false);
        objUploadImg.xhr[id].addEventListener("error", failed, false);
        objUploadImg.xhr[id].open("POST", uploadUrl);
        objUploadImg.xhr[id].send(formData);
    };

    // �����ϴ�ϵͳ��ʾ
    objUploadImg.checkUploadBySysVer = function() {
        var mb_os = objGlobal.checkUA();
        if (mb_os.ios && mb_os.version.toString() < '6.0') {
            objGlobal.DIC.dialog({content:'�ֻ�ϵͳ��֧�ִ�ͼ����������ios6.0����', autoClose:true});
            return false;
        }

        if (mb_os.wx && mb_os.wxVersion.toString() < '5.2') {
            objGlobal.DIC.dialog({content:'��ǰ΢�Ű汾��֧�ִ�ͼ�������������°�', autoClose:true});
            return false;
        }
        return true;
    };

    //�����Ƿ���Զ�ͼ�ϴ����ɶ�Ӧ��input
    objUploadImg.uploadAddInput = function(){
        var input = "", fistInput = "";
        if(objUploadImg.uploadPicMore){
            input = '<input type="file" class="mbupload_on mbupload_uploadFile" accept="image/*" multiple="">';
            fistInput = '<input type="file" class="fistUpload" accept="image/*" multiple="">';
        }else{
            input = '<input type="file" class="mbupload_on mbupload_uploadFile" accept="image/*" single="">';
            fistInput = '<input type="file" class="fistUpload" accept="image/*" single="">';
        }
        $(objUploadImg.formHtmlId + ' .mbupload_addPic').append(input);
        $(objUploadImg.formHtmlId + ' .iconSendImg').append(fistInput);
    };

    //ʣ���ϴ���
    objUploadImg.uploadRemaining = function(){
        var uploadNum = 0;
        uploadNum = $(objUploadImg.formHtmlId + ' .mbupload_photoList').find('li').length;

        var canOnlyUploadNum = objUploadImg.maxUpload;
        
        if(uploadNum <= objUploadImg.maxUpload)
        {
            canOnlyUploadNum = objUploadImg.maxUpload - uploadNum + 1;
        }
        else
        {
            canOnlyUploadNum = 0;
        }

        //���ϴ���������ʾ��һ�ϴ�ҳ��
        if(canOnlyUploadNum == objUploadImg.maxUpload)
        {
            $(objUploadImg.formHtmlId + ' .mbupload_photoList').hide();
            $(objUploadImg.formHtmlId + ' .mbupload_bgimg').show();
        }

        //����ʣ����ϴ�ͼƬ��
        $(objUploadImg.formHtmlId + ' .mbupload_onlyUploadNum').html(canOnlyUploadNum);
    };

    // ���ͼƬ��С
    objUploadImg.checkPicSize = function(file) {
        var uploadPicSize = objUploadImg.uploadPicSize*1024*1024;
        if (file.size > uploadPicSize) {
            return false;
        }
        return true;
    };

    // ���ͼƬ����
    objUploadImg.checkPicType = function(file) {
        var photoReg = (/\.png$|\.bmp$|\.jpg$|\.jpeg$|\.gif$/i);
        if(!photoReg.test(file.name)){
           return false;
        }else{
            return true;
        }
    };

    var uploadTimer = null;

    var initUpload = function()
    {
        // �ϴ�ͼƬ�İ�
        $(objUploadImg.formHtmlId + ' .mbupload_addImg').on("click", function(){
            if(!objUploadImg.checkUploadBySysVer()){
                return false;
            }
        });

        $(objUploadImg.formHtmlId + ' .mbupload_uploadFile').on("click", function(){
            var thisObj = $(this);
            if (objUploadImg.isEncoderBusy) {
                return false;
            }
            else if (objUploadImg.isBusy) {
                objGlobal.DIC.dialog({content:'�ϴ��У����Ժ����', autoClose:true});
                return false;
            }
            else if (objUploadImg.isMoreBusy) {
                objGlobal.DIC.dialog({content:'�ϴ��У����Ժ����', autoClose:true});
                return false;
            }
        });

        //�״ε��ͼƬ��ͼ�꣬����һ���ֻ���Ĭ���ϴ��¼�
        $('body').on('change', objUploadImg.formHtmlId + ' .fistUpload', function(e){
            $(objUploadImg.formHtmlId + ' .mbupload_photoList').show();
            $(objUploadImg.formHtmlId + ' .mbupload_bgimg').hide();
        });

        // �ļ��������仯ʱ
        $('body').on('change', objUploadImg.formHtmlId + ' .mbupload_uploadFile,' + objUploadImg.formHtmlId + ' .fistUpload', function(e) {
            //ִ��ͼƬԤ����ѹ����ʱ��
            uploadTimer = setInterval(function() {
                // Ԥ��
                setTimeout(function() {
                    if (!objUploadImg.isEncoderBusy && objUploadImg.previewQueue.length) {
                        var jobId = objUploadImg.previewQueue.shift();
                        objUploadImg.isEncoderBusy = true;
                        objUploadImg.uploadPreview(jobId);
                    }
                }, 1);

                // �ϴ�
                setTimeout(function() {
                    if (!objUploadImg.isBusy && objUploadImg.uploadQueue.length) {
                        var jobId = objUploadImg.uploadQueue.shift();
                        objUploadImg.isBusy = true;
                        if(objUploadImg.uploadPicMore){
                            objUploadImg.isMoreBusy = true;
                        }
                        objUploadImg.createUpload(jobId, objUploadImg.formHtmlId, uploadTimer);
                    }
                }, 10);
            }, 300);

            e = e || window.event;
            var fileList = e.target.files;

            if (!fileList.length) {
                //����ʣ���ϴ���
                objUploadImg.uploadRemaining();
                $(this).val('');
                return false;
            }

            if (objUploadImg.uploadPicMore && (fileList.length > objUploadImg.onceMaxUpload)) {
                objGlobal.DIC.dialog({content:'�ϴ�ͼƬһ�����ֻ��ѡ' + objUploadImg.onceMaxUpload + '��', autoClose:true});
                //����ʣ���ϴ���
                objUploadImg.uploadRemaining();
                $(this).val('');
                return false;
            }

            if (objUploadImg.uploadPicMore && (fileList.length > (objUploadImg.maxUpload - objUploadImg.countUpload()))) {
                objGlobal.DIC.dialog({content:'�����ֻ���ϴ�' + objUploadImg.maxUpload + '����Ƭ', autoClose:true});
                //����ʣ���ϴ���
                objUploadImg.uploadRemaining();
                $(this).val('');
                return false;
            }

            for (var i = 0; i < fileList.length; i++) {
                if (objUploadImg.countUpload() >= objUploadImg.maxUpload) {
                    objGlobal.DIC.dialog({content:'�����ֻ���ϴ�' + objUploadImg.maxUpload + '����Ƭ', autoClose:true});
                    //����ʣ���ϴ���
                    objUploadImg.uploadRemaining();
                    $(this).val('');
                    break;
                }

                var file = fileList[i];

                if (!objUploadImg.checkPicType(file)) {
                    objGlobal.DIC.dialog({content:'�ϴ���Ƭ��ʽ��֧��', autoClose:true});
                    //����ʣ���ϴ���
                    objUploadImg.uploadRemaining();
                    $(this).val('');
                    continue;
                }
                // console.log(file);
                if (!objUploadImg.checkPicSize(file)) {
                    objGlobal.DIC.dialog({content:'ͼƬ��С����'+ objUploadImg.uploadPicSize + 'MB', autoClose:true});
                    //����ʣ���ϴ���
                    objUploadImg.uploadRemaining();
                    $(this).val('');
                    continue;
                }

                var id = Date.now() + i;
                // ���ӵ��ϴ�������, �ϴ���ɺ��޸�Ϊ true
                objUploadImg.uploadInfo[id] = {
                    oldFileInfo: file,
                    file: file,
                    isDone: false,
                };

                var html = '<li id="li' + id + '"><div class="mbupload_photoCut"><img src="' + objUploadImg.uploadDefaultImgUrl + '" class="attchImg" alt="photo"></div>' +
                        '<div class="mbupload_maskLay"></div>' +
                        '<div class="mbupload_maskTxt">ͼƬѹ����...</div>' +
                        '<a href="javascript:;" class="mbupload_cBtn mbupload_pa mbupload_db" title="" _id="'+id+'">�ر�</a></li>';
                $(objUploadImg.formHtmlId + ' .mbupload_addPic').before(html);

                objUploadImg.previewQueue.push(id);

                // ͼƬ�Ѿ��ϴ��� ������� ���������� + ��
                if (objUploadImg.countUpload() >= objUploadImg.maxUpload) {
                    $(objUploadImg.formHtmlId + ' .mbupload_addPic').hide();
                }
                //����ʣ���ϴ���
                setTimeout(function(){
                    objUploadImg.uploadRemaining();
                }, 400);
            }
            // ����������
            $(this).val('');
        });

        $(objUploadImg.formHtmlId + ' .mbupload_photoList').on('click', '.mbupload_cBtn', function() {
            var id = $(this).attr('_id');

            // ȡ���������
            if (objUploadImg.xhr[id]) {
                objUploadImg.xhr[id].abort();
            }
			//ɾ����������ͼƬ
			var title = $(objUploadImg.formHtmlId + ' #input' + id).val();
			$.get("plugin.php?id=summer_lottery:img"+""+"&action=delete&title="+title,function(res){
				console.log(res);
			})
            // ͼƬɾ��
            $(objUploadImg.formHtmlId + ' #li' + id).remove();
            // ����ɾ��
            $(objUploadImg.formHtmlId + ' #input' + id).remove();
            objUploadImg.uploadInfo[id] = null;
            // ͼƬ�����ˣ���ʾ+��
            if (objUploadImg.countUpload() < objUploadImg.maxUpload) {
                $(objUploadImg.formHtmlId + ' .mbupload_addPic').show();
            }
            //����ʣ���ϴ���
            objUploadImg.uploadRemaining();

            //��ɾ������ͼƬ���������ͼƬ��ͼ��
            if($(objUploadImg.formHtmlId + ' .mbupload_photoList').find('li').length < 2){
                $(objUploadImg.formHtmlId + ' .mbupload_photoList').hide();
                $(objUploadImg.formHtmlId + ' .mbupload_bgimg').show();
            }
        });        
    };

    objUploadImg.uploadAddInput();
    objUploadImg.uploadRemaining();
    initUpload();
};