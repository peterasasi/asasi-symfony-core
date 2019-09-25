## 基类控制
1. 可用于 h5网页 基类: BaseH5Controller   
2. 可用于 需要登录的api 基类: BaseNeedLoginController   
3. 可用于 api 基类: BaseSymfonyApiController   

## 必须实现的接口 并注册为Service

1. clients对象 ClientsInterface   
2. 获取clients对象 GetClientsInterface
3. 用户账户对象 UserAccountInterface
4. 用户账户操作对象 UserAccountServiceInterface
3. 用户信息对象 UserProfileInterface
4. 用户信息操作对象 UserProfileServiceInterface
5. 登录会话操作对象 LoginSessionInterface

## 且手动绑定Service
YAML例子:   
```
Dbh\SfCoreBundle\Common\LoginSessionInterface: '@App\Service\LoginSessionService'
Dbh\SfCoreBundle\Common\ClientsInterface: '@App\Service\Clients'
Dbh\SfCoreBundle\Common\GetClientsInterface: '@App\Service\ClientsService'
```
