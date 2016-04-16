<?php /** @var ADNBP $this */?>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/angular_material/0.9.4/angular-material.min.css">

<md-whiteframe class="container-fluid" style="margin-top: 50px;" ng-app="cf-social-network" flex layout="row" layout-sm="column" ng-controller="SocialController">
    <md-content flex="30" flex-sm="100" layout="column">
        <md-switch ng-model="apis.google" aria-label="Google Plus">
            Google Plus Api
        </md-switch>
        <md-switch ng-model="apis.facebook" aria-label="Facebook">
            Facebook Api
        </md-switch>
        <md-switch ng-model="apis.twitter" aria-label="Twitter">
            Twitter Api
        </md-switch>
        <md-switch ng-model="apis.pinterest" aria-label="Pinterest">
            Pinterest Api
        </md-switch>
        <md-switch ng-model="apis.instagram" aria-label="Instagram">
            Intagram Api
        </md-switch>
        <md-switch ng-model="apis.linkedin" aria-label="Linkedin">
            LinkedIn Api
        </md-switch>
    </md-content>
    <md-content flex="40" flex-sm="100" layout="column">
        <md-card flex ng-show="apis.google">
            <img ng-src="http://www.aokmarketing.com/wp-content/uploads/2015/07/Screen-Shot-2014-03-04-at-5.50.44-PM.png" class="md-card-image" alt="Google +">
            <md-card-content>
                <md-input-container>
                    <label>Google Api Key</label>
                    <input ng-model="google.client" ng-init="google.client = '<?=$_SESSION["google_apikeys"]["client"]?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google Api Secret</label>
                    <input ng-model="google.secret" ng-init="google.secret = '<?=$_SESSION["google_apikeys"]["secret"]?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google Api Auth Access Token</label>
                    <input ng-model="google.access_token" ng-init="google.access_token = '<?=getFromArray("access_token", $google)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google Api Auth Token Type</label>
                    <input ng-model="google.token_type" ng-init="google.token_type = '<?=getFromArray("token_type", $google)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google Api Auth Expiration</label>
                    <input ng-model="google.expires_in" ng-init="google.expires_in = '<?=getFromArray("expires_in", $google)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google Api Auth Created</label>
                    <input ng-model="google.created" ng-init="google.created = '<?=getFromArray("created", $google)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google Api Auth Refresh Token</label>
                    <input ng-model="google.refresh_token" ng-init="google.refresh_token = '<?=getFromArray("refresh_token", $google)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Text for testing export stream / comment to Google +</label>
                    <input ng-model="google.export_content" ng-init="google.export_content = '<?=getFromArray("export_content", $google)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Google User ID: <?=(count($google) > 0)?$google["user"]->getIdUser():""?></label>
                </md-input-container>
                <md-input-container>
                    <label>Google User Full Name: <?=(count($google) > 0)?$google["user"]->getFullName():""?></label>
                </md-input-container>
                <md-input-container>
                    <label>Google User Email: <?=(count($google) > 0)?$google["user"]->getEmail():""?></label>
                </md-input-container>
                <md-input-container>
                    <label>Google User Image: <img src="<?=(count($google) > 0)?$google["user"]->getImageUrl():""?>" width="50" height="50"/></label>
                </md-input-container>
            </md-card-content>
            <div class="md-actions" flex="100" layout="row" layout-align="center center">
                <md-button class="md-raised md-primary" ng-click="getSocialData('Google')">Get social data</md-button>
            </div>
        </md-card>
        <md-card flex ng-show="apis.facebook">
            <img ng-src="http://factual-content.s3.amazonaws.com/marketing/blog/embedded/2015-08-31-facebook-image1.jpg" class="md-card-image" alt="Facebook">
            <md-card-content>
                <md-input-container>
                    <label>Facebook Api Key</label>
                    <input ng-model="facebook.apiKey" ng-init="facebook.apiKey = '<?=$this->getConf("FacebookOauth_APP_ID")?>'">
                </md-input-container>
                <md-input-container>
                    <label>Facebook Api Secret</label>
                    <input ng-model="facebook.secret" ng-init="facebook.secret = '<?=$this->getConf("FacebookOauth_APP_SECRET")?>'">
                </md-input-container>
                <md-input-container>
                    <label>Facebook Api User Token</label>
                    <input ng-model="facebook.facebook_access_token" ng-init="facebook.facebook_access_token = '<?=getFromArray("facebook_access_token", $facebook)?>'">
                </md-input-container>
            </md-card-content>
            <div class="md-actions" flex="100" layout="row" layout-align="center center">
                <md-button class="md-raised md-primary" ng-click="getSocialData('Facebook')">Get Social Data</md-button>
            </div>
        </md-card>
        <md-card flex ng-show="apis.twitter">
            <img ng-src="http://www.av.mavt.ethz.ch/wp-content/uploads/2015/02/twitter-logo-300x112.png" class="md-card-image" alt="Twitter">
            <md-card-content>
                <md-input-container>
                    <label>Twitter Api Key</label>
                    <input ng-model="twitter.consumer_key" ng-init="twitter.consumer_key = '<?=$this->getConf("TwitterOauth_KEY")?>'">
                </md-input-container>
                <md-input-container>
                    <label>Twitter Api Secret</label>
                    <input ng-model="twitter.consumer_secret" ng-init="twitter.consumer_secret = '<?=$this->getConf("TwitterOauth_SECRET")?>'">
                </md-input-container>
                <md-input-container>
                    <label>Twitter Api User Token</label>
                    <input ng-model="twitter.oauth_access_token" ng-init="twitter.oauth_access_token = '<?=getFromArray("oauth_access_token", $twitter)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Twitter Api User Token OAuth</label>
                    <input ng-model="twitter.oauth_access_token_secret" ng-init="twitter.oauth_access_token_secret = '<?=getFromArray("oauth_access_token_secret", $twitter)?>'">
                </md-input-container>
            </md-card-content>
            <div class="md-actions" flex="100" layout="row" layout-align="center center">
                <md-button class="md-raised md-primary" ng-click="getSocialData('Twitter')">Get Social Data</md-button>
            </div>
        </md-card>
        <md-card flex ng-show="apis.linkedin">
            <img ng-src="http://cdn2.business2community.com/wp-content/uploads/2013/08/logo-linkedin2.png" class="md-card-image" alt="LinkedIn">
            <md-card-content>
                <md-input-container>
                    <label>LinkedIn Api Key</label>
                    <input ng-model="linkedin.apiKey" ng-init="linkedin.apiKey = '<?=$this->getConf("LinkedInOauth_KEY")?>'">
                </md-input-container>
                <md-input-container>
                    <label>LinkedIn Api Secret</label>
                    <input ng-model="linkedin.secret" ng-init="linkedin.secret = '<?=$this->getConf("LinkedInOauth_SECRET")?>'">
                </md-input-container>
                <md-input-container>
                    <label>LinkedIn Api User Token</label>
                    <input ng-model="linkedin.token">
                </md-input-container>
                <md-input-container>
                    <label>LinkedIn Api User Token OAuth</label>
                    <input ng-model="linkedin.oauthToken">
                </md-input-container>
            </md-card-content>
            <div class="md-actions" flex="100" layout="row" layout-align="center center">
                <md-button class="md-raised md-primary" ng-click="getSocialData('LinkedIn')">Get Social Data</md-button>
            </div>
        </md-card>
        <md-card flex ng-show="apis.instagram">
            <img ng-src="http://images.wondershare.com/images/multimedia/video-editor/instagram-logo.png" class="md-card-image" alt="Instagram">
            <md-card-content>
                <md-input-container>
                    <label>Instagram Api Client ID</label>
                    <input ng-model="instagram.client" ng-init="instagram.client = '<?=$_SESSION["instagram_apikeys"]["client"]?>'">
                </md-input-container>
                <md-input-container>
                    <label>Instagram Api Client Secret</label>
                    <input ng-model="instagram.secret" ng-init="instagram.secret = '<?=$_SESSION["instagram_apikeys"]["secret"]?>'">
                </md-input-container>
                <md-input-container>
                    <label>Instagram Api Access Token</label>
                    <input ng-model="instagram.access_token" ng-init="instagram.access_token = '<?=getFromArray("access_token", $instagram)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Text for testing export stream / comment to Instagram media</label>
                    <input ng-model="instagram.export_content" ng-init="instagram.export_content = '<?=getFromArray("export_content", $instagram)?>'">
                </md-input-container>
                <md-input-container>
                    <label>Instagram User ID: <?=(count($instagram) > 0)?$instagram["user"]->getIdUser():""?></label>
                </md-input-container>
                <md-input-container>
                    <label>Instagram User Full Name: <?=(count($instagram) > 0)?$instagram["user"]->getFullName():""?></label>
                </md-input-container>
                <md-input-container>
                    <label>Instagram User Email: <?=(count($instagram) > 0)?$instagram["user"]->getEmail():""?></label>
                </md-input-container>
                <md-input-container>
                    <label>Instagram User Image: <img src="<?=(count($instagram) > 0)?$instagram["user"]->getImageUrl():""?>" width="50" height="50"/></label>
                </md-input-container>
            </md-card-content>
            <div class="md-actions" flex="100" layout="row" layout-align="center center">
                <md-button class="md-raised md-primary" ng-click="getSocialData('Instagram')">Get Social Data</md-button>
            </div>
        </md-card>
        <md-card flex ng-show="apis.pinterest">
            <img ng-src="http://marketingland.com/wp-content/ml-loads/2013/12/Pinterest.png" class="md-card-image" alt="Pinterest">
            <md-card-content>
                <md-input-container>
                    <label>Pinterest Api Key</label>
                    <input ng-model="pinterest.apiKey" ng-init="pinterest.apiKey = '<?=$this->getConf("PinterestOauth_CLIENT_ID")?>'">
                </md-input-container>
                <md-input-container>
                    <label>Pinterest Api Secret</label>
                    <input ng-model="pinterest.secret" ng-init="pinterest.secret = '<?=$this->getConf("PinterestOauth_CLIENT_SECRET")?>'">
                </md-input-container>
                <md-input-container>
                    <label>Pinterest Api User Token</label>
                    <input ng-model="pinterest.token">
                </md-input-container>
                <md-input-container>
                    <label>Pinterest Api User Token OAuth</label>
                    <input ng-model="pinterest.oauthToken">
                </md-input-container>
            </md-card-content>
            <div class="md-actions" flex="100" layout="row" layout-align="center center">
                <md-button class="md-raised md-primary" ng-click="getSocialData('Pinterest')">Get Social Data</md-button>
            </div>
        </md-card>
    </md-content>
    <md-content flex="40" flex-sm="100" layout="column">
        <md-card flex="30" flex-sm="100">
            <md-whiteframe class="md-whiteframe-2dp" flex="100" layout layout-align="center center">
                <h3>Social Network Data</h3>
            </md-whiteframe>
            <md-card-content layout="column" layout-fill layout-align="center start">
                <md-progress-circular md-mode="indeterminate" ng-show="loading"></md-progress-circular>
            </md-card-content>
        </md-card>
        <div layout="row" layout-padding layout-wrap layout-fill style="padding-bottom: 32px;" ng-cloak ng-repeat="data in socialData" ng-show="data.loaded">
            <!--<md-whiteframe class="md-whiteframe-1dp" flex-sm="45" flex-gt-sm="35" flex-gt-md="100" layout layout-align="center center">
                <span>Social Network <strong>{{data.social}}</strong> has <strong>{{data.followers}}</strong> followers</span>
            </md-whiteframe>-->
            <md-whiteframe class="md-whiteframe-2dp" flex-sm="45" flex-gt-sm="35" flex-gt-md="100" layout layout-align="center center">
                <span>Social Network <strong>{{data.social}}</strong> has <strong>{{data.count}}</strong> images:</span>
            </md-whiteframe>
            <md-whiteframe class="md-whiteframe-3dp" flex-sm="45" flex-gt-sm="35" flex-gt-md="100" layout layout-align="center center" ng-repeat="image in data.images" ng-switch on="$index % 4">
                <span ng-switch-when="0" style="padding-right:30px">
                    <img alt="{{data.images[$index].title}}" src="{{data.images[$index].name}}" width="50" height="50"/>
                </span>
                <span ng-show="data.images[$index+1]" style="padding-right:30px">
                    <span ng-switch-when="0">
                        <img alt="{{data.images[$index+1].title}}" src="{{data.images[$index+1].name}}" width="50" height="50"/>
                    </span>
                </span>
                <span ng-show="data.images[$index+2]" style="padding-right:30px">
                    <span ng-switch-when="0">
                        <img alt="{{data.images[$index+2].title}}" src="{{data.images[$index+2].name}}" width="50" height="50"/>
                    </span>
                </span>
                <span ng-show="data.images[$index+3]">
                    <span ng-switch-when="0">
                        <img alt="{{data.images[$index+3].title}}" src="{{data.images[$index+3].name}}" width="50" height="50"/>
                    </span>
                </span>
            </md-whiteframe>
        </div>
    </md-content>
</md-whiteframe>

<!-- Angular Material Dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-animate.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular-aria.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angular_material/0.9.4/angular-material.min.js"></script>
<script type="text/javascript">
    (function(){
        var app = angular.module("cf-social-network", ["ngMaterial"]);
        app.controller("SocialController", ["$scope", "$http", "$log", "$location", "$window", "$mdDialog",
            function($scope, $http, $log, $location, $window, $mdDialog){
                $scope.dataLoaded = false;
                $scope.loading = false;
                $scope.socialData = {};

                /**
                 * CFSocialNetwork service that extract from apis user followers
                 * @param socialNetwork
                 */
                function getSocialData(socialNetwork) {
                    if(!$scope.loading) {
                        $scope.loading = true;
                        $scope.dataLoaded = false;
                        var social = socialNetwork.toLowerCase();
                        $log.info("Get social data for " + socialNetwork);
                        var data = $scope[social];
                        data['social'] = socialNetwork;
                        $http.post("/socialnetworks", data)
                            .then(function(response) {
                                $scope.loading = false;
                                $scope.dataLoaded = true;
                                $log.info(response.data);
                                $scope.socialData[socialNetwork] = response.data;
                                $scope.socialData[socialNetwork].loaded = true;
                                $location.path("/");
                            }, function(errorResponse) {
                                $log.warn(errorResponse);
                                $scope.loading = false;
                                $scope.dataLoaded = false;
                                if(errorResponse.status == 401) {
                                    $log.info("Authorize social network to work with it");
                                    $window.location = errorResponse.statusText;
                                } else if(errorResponse.status === 302) {
                                    $window.location = errorResponse.statusText;
                                } else {
                                    $mdDialog.show(
                                        $mdDialog.alert()
                                            .clickOutsideToClose(true)
                                            .title('An error was ocurred!! Status code ' + errorResponse.status)
                                            .content(errorResponse.statusText)
                                            .ariaLabel(errorResponse.statusText)
                                            .ok('Close')
                                    );
                                }
                            });
                    }

                }

                $scope.getSocialData = getSocialData;

            }
        ])
    })();
</script>