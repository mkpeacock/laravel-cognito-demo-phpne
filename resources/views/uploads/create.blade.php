@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Upload a file</div>

                    <div class="card-body">
                        <form>
                            <input type="file" id="file_upload" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://sdk.amazonaws.com/js/aws-sdk-2.213.1.min.js"></script>
    <script type="text/javascript">

        document.getElementById('file_upload').onchange = function () {

            AWS.config.update({
                region: '{{ $bucketRegion }}',
                credentials: new AWS.CognitoIdentityCredentials({
                    IdentityPoolId: '{{ $identityPoolId }}',
                    IdentityId: '{{ $cognitoIdentityId }}',
                    Logins: {
                        'cognito-identity.amazonaws.com': '{!! $cognitoToken !!}'
                    }
                })
            });

            var s3 = new AWS.S3({
                apiVersion: '2006-03-01',
                params: {Bucket: '{{ $bucketName }}'}
            });

            var files = document.getElementById('file_upload').files;
            if (!files.length) {
                return alert('Please choose a file to upload first.');
            }

            var file = files[0];
            var fileName = file.name;
            var fileKey = '{{ $bucketPrefix }}-' + fileName;

            s3.upload({
                Bucket: '{{ $bucketName }}',
                Key: fileKey,
                Body: file,
                ACL: 'public-read'
            }, function(err, data) {
                if (err) {
                    console.log(err);
                    return alert('There was an error uploading your image: ', err.message);
                }

                alert('Uploaded to: ' + data.Location);
            }).on('httpUploadProgress', function (progress) {
                console.log(progress);
            });
        };
    </script>
@endpush
