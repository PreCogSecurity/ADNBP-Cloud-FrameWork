<?php /** @var ADNBP $this */ ?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.9.4/angular-material.min.css">

<div class="container-fluid" style="margin-top: 50px;" ng-app="cf-push-notifications" flex layout="row"
     layout-sm="column" ng-controller="PushController">
    <md-content flex layout="column">
        <h1>Push notification form example</h1>
        <md-card flex layout-padding>
            <md-card-content>
                <form onsubmit="return false;" novalidate name="pushForm" layout="column" flex="100">
                    <md-radio-group ng-model="push.type">
                        <md-radio-button value="apns" class="md-primary">Apple(APNS)</md-radio-button>
                        <md-radio-button value="gcm" class="md-primary">Android(GCM)</md-radio-button>
                        <md-radio-button value="mpns" class="md-primary">Windows Phone(MPNS)</md-radio-button>
                    </md-radio-group>
                    <fieldset ng-show="push.type == 'apns'" layout="row" flex>
                        <legend>Apple Required Fields</legend>
                        <md-input-container>
                            <label>Certificate Path</label>
                            <input type="text" ng-model="push.certPath" ng-required="push.type == 'apns'">
                        </md-input-container>
                        <md-input-container>
                            <label>Passphrase for certificate</label>
                            <input type="password" ng-model="push.phrase" ng-required="push.type == 'apns'">
                        </md-input-container>
                    </fieldset>
                    <fieldset ng-show="push.type == 'gcm'" layout="row" flex>
                        <legend>Android Required Fields</legend>
                        <md-input-container>
                            <label>Key</label>
                            <input type="password" ng-model="push.key" ng-required="push.type == 'gcm'">
                        </md-input-container>
                    </fieldset>
                    <fieldset ng-show="push.type != ''">
                        <legend>Payload</legend>
                        <div flex="100" layout>
                            <md-input-container flex="30">
                                <label>Url</label>
                                <input type="text" ng-model="push.url" required>
                            </md-input-container>
                            <md-input-container flex="30" style="margin-top: 7px;">
                                <md-select ng-model="push.pType" placeholder="Notification Type">
                                    <md-option ng-repeat="type in types" value="{{type.id}}">{{type.name}}</md-option>
                                </md-select>
                            </md-input-container>
                            <md-input-container flex="30">
                                <label>Badge</label>
                                <input type="number" md-model="push.badge" ng-init="push.badge = 0" min="0" value="0">
                            </md-input-container>
                        </div>
                        <div flex="100" layout>
                            <md-input-container flex="50">
                                <label>Message</label>
                                <textarea columns="1" required ng-model="push.payload" md-maxlength="1024"></textarea>
                            </md-input-container>
                            <md-input-container flex="50">
                                <label>Device Token</label>
                                <input required ng-model="push.device" >
                            </md-input-container>
                        </div>
                    </fieldset>
                    <div class="md-actions" flex="100" layout="row" layout-align="center center">
                        <md-button class="md-raised md-primary" id="send-push" ng-click="doPushNotification()">Try push notification</md-button>
                    </div>
                </form>
            </md-card-content>
        <md-card>
    </md-content>
</div>

<!-- Angular Material Dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-animate.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-aria.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angular_material/0.9.4/angular-material.min.js"></script>
<script type="text/javascript">
    (function () {
        var app = angular.module("cf-push-notifications", ["ngMaterial"]);
        app.controller("PushController", ["$scope", "$http", "$log", "$location", "$window", "$mdDialog", "$compile",
            function ($scope, $http, $log, $location, $window, $mdDialog, $compile) {
                $scope.types = [
                    {"id": 0,"name":"General alerts"},
                    {"id": 1,"name":"Order related notifications"},
                    {"id": 2,"name":"Product related notifications"},
                    {"id": 3,"name":"Rating related notifications"},
                    {"id": 4,"name":"Completed withdraw notifications"},
                    {"id": 5,"name":"Chat notifications"},
                ];
                /**
                 * Action for pushing messages
                 */
                function doPushNotification() {
                    var form = $scope.pushForm;
                    if (form.$valid) {
                        $http.post('/pushMessages', $scope.push, {
                            headers: {
                                'X-CLOUDFRAMEWORK-SECURITY': '<?=$this->generateCloudFrameWorkSecurityString('test', microtime(true), ';We*Ef')?>'
                            }
                        })
                            .then(function (response) {
                                var _response = response.data;
                                if (!_response.success) {
                                    $mdDialog.show(
                                        $mdDialog.alert()
                                            .clickOutsideToClose(true)
                                            .title('Push notifications failed')
                                            .content(_response.message)
                                            .ariaLabel(_response.message)
                                            .ok('Thanks!')
                                    );
                                } else {
                                    $mdDialog.show(
                                        $mdDialog.alert()
                                            .clickOutsideToClose(true)
                                            .title('Push notifications sended')
                                            .content(_response.message)
                                            .ariaLabel(_response.message)
                                            .ok('Close')
                                    );
                                }
                                $log.warn(response);
                            }, function (errorResponse) {
                                $log.debug(errorResponse);
                                if (errorResponse.status == 401) {
                                    $log.info("Authorize social network to work with it");
                                    $window.location = errorResponse.statusText;
                                } else if (errorResponse.status === 302) {
                                    $window.location = errorResponse.statusText;
                                } else {
                                    var errorMessage = errorResponse.statusText;
                                    try {
                                        errorMessage = errorResponse.data.message;
                                    } catch(err) { }
                                    $mdDialog.show(
                                        $mdDialog.alert()
                                            .clickOutsideToClose(true)
                                            .title('An error was ocurred!! Status code ' + errorResponse.status)
                                            .content(errorMessage)
                                            .ariaLabel(errorResponse.statusText)
                                            .ok('Close')
                                    );
                                }
                            });
                    } else if (form.$error.required) {
                        var required = form.$error.required.length || 0;
                        $mdDialog.show(
                            $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Form error')
                                .content(required == 1 ? 'There is one error in the form, please complete a required field' : 'There are ' + required + ' uncompleted required fields, please complete those fields')
                                .ariaLabel('validation-error')
                                .ok('Close')
                        );
                    } else {
                        var wrongFields = '', sep = '', errN = 0;
                        for (var e in form.$error) {
                            wrongFields += sep + e;
                            sep = ', ';
                            errN++;
                        }
                        $mdDialog.show(
                            $mdDialog.alert()
                                .clickOutsideToClose(true)
                                .title('Form error')
                                .content(errN == 1 ? 'Field ' + wrongFields + ' has an error, please correct this and submit the form' : 'Fields ' + wrongFields + ' have errors, please correct and submit the form again')
                                .ariaLabel('validation-error')
                                .ok('Close')
                        );
                    }
                }

                $scope.doPushNotification = doPushNotification;
            }
        ]);
    })();
</script>