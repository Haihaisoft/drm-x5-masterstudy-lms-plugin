# DRM-X 5.0 MasterStudy LMS Plugin - WordPress Video DRM and Course Protection

[![DRM-X 5.0](https://img.shields.io/badge/DRM--X-5.0-155EEF)](https://www.drm-x.com/en/products/drm-x-5.0/features)
[![Plugin version](https://img.shields.io/badge/plugin-v1.0.0-2E8B57)](./drmx5-masterstudy-lms.php)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759B)](https://wordpress.org/)
[![MasterStudy LMS](https://img.shields.io/badge/LMS-MasterStudy-2271B1)](https://stylemixthemes.com/wordpress-lms-plugin/)

**DRM-X 5.0 Integration for MasterStudy LMS** connects WordPress course access with DRM-X digital rights management. It verifies the learner's active MasterStudy LMS user-course record before DRM-X issues a license for encrypted video playback in the ZJGet secure browser.

This WordPress LMS DRM plugin is intended for paid e-learning, membership courses, academies, and corporate training that need video encryption, course enrollment verification, watermarking, license revocation, and stronger anti-piracy controls.

> [Jump to the Chinese introduction](#中文简介).

## Why use DRM-X with MasterStudy LMS?

MasterStudy controls access to WordPress course pages. DRM-X 5.0 protects the media file itself and requires a valid license before playback, helping reduce unauthorized sharing of copied video URLs or downloaded course files. Available DRM-X Rights can include dynamic watermarking, device controls, license revocation, virtual-machine restrictions, and screen-recording protection according to the selected account and configuration.

Learn more about [DRM-X 5.0 digital content protection](https://www.drm-x.com/en/products/drm-x-5.0/features).

## Features

- Requires WordPress sign-in before DRM-X license delivery.
- Requires an active MasterStudy LMS user-course record for an assigned course.
- Supports one or multiple WordPress course post IDs such as `123-124-208`.
- Automatically creates a matching DRM-X user when needed.
- Rejects existing DRM-X users who are revoked or locked.
- Supports DRM-X International (`5.drm-x.com`) and China (`5.drm-x.cn`).
- Uses the License Profile RightsID posted by ZJGet without changing DRM-X permissions.
- Optionally uses a fixed RightsID and calls `UpdateRightWithDisableVirtualMachine`.
- Adds the `[zjget-player]URL[/zjget-player]` WordPress shortcode.
- Disables the player in editing and preview contexts.
- Includes English and Simplified Chinese interface text.

## Requirements

- WordPress 5.8 or later
- PHP 7.4 or later with SOAP enabled
- MasterStudy LMS installed and activated
- An HTTPS WordPress website
- A DRM-X 5.0 account, License Profile, Rights, and user group
- ZJGet secure browser for protected playback

## Download and documentation

- [Download DRM-X 5.0 MasterStudy LMS Plugin v1.0.0](https://github.com/Haihaisoft/drm-x5-masterstudy-lms-plugin/raw/refs/heads/main/dist/drmx5-masterstudy-lms-1.0.0.zip)
- [English PDF User Guide](https://github.com/Haihaisoft/drm-x5-masterstudy-lms-plugin/raw/refs/heads/main/docs/DRM-X-5.0-masterstudy-lms-integration-user-guide-v1.0.0.pdf)
- [中文 PDF 使用指南](https://github.com/Haihaisoft/drm-x5-masterstudy-lms-plugin/raw/refs/heads/main/docs/DRM-X-5.0-masterstudy-lms-integration-user-guide-zh-CN-v1.0.0.pdf)

## Installation

1. Install and activate MasterStudy LMS.
2. Open **Plugins > Add New Plugin > Upload Plugin** in WordPress.
3. Upload `drmx5-masterstudy-lms-1.0.0.zip` and activate it.
4. Open **Settings > DRM-X 5.0**.

Manual plugin directory:

```text
wp-content/plugins/drmx5-masterstudy-lms/
```

## Plugin settings

| Setting | Description |
| --- | --- |
| `AdminEmail` | DRM-X 5.0 administrator email address. |
| `WebServiceAuthStr` | Web Service Authorization String configured in DRM-X. Keep it secret. |
| `GroupID` | DRM-X user group used for user creation and licensing. |
| Service area | International uses `5.drm-x.com`; China uses `5.drm-x.cn`. |
| RightsID mode | Read License Profile RightsID from POST, or update and use a fixed RightsID. |
| Fixed RightsID | Required only when fixed mode is selected. |

### RightsID modes

**Read RightsID from ZJGet POST data:** ZJGet submits the RightsID configured through the DRM-X License Profile. The plugin obtains the license with that ID and does not update the DRM-X permission.

**Fixed RightsID and automatic update:** before every license request, the plugin calls `UpdateRightWithDisableVirtualMachine`. It uses the start and end times in the MasterStudy user-course record. If a valid value is unavailable, the request time and a ten-year expiration are used. `ExpirationAfterFirstUse` is `-1`.

## DRM-X License Profile setup

In **DRM-X 5.0 Account Settings > Website Integration Settings**, enter the same `WebServiceAuthStr` and use:

```text
https://your-wordpress-domain.example/wp-content/plugins/drmx5-masterstudy-lms/licstore5.php
```

Keep the endpoint filename `licstore5.php`. Use the same HTTPS hostname that learners use for WordPress sign-in.

### Course ID

Edit the MasterStudy course. If the WordPress URL contains:

```text
/wp-admin/post.php?post=123&action=edit
```

enter `123` in the DRM-X License Profile **Your Product ID** field. Use the numeric course post ID, not a lesson ID, slug, product ID, or URL.

Multiple courses:

```text
123-124-208
```

The plugin checks IDs from left to right and uses the first course with an active MasterStudy record. Access to one listed course does not grant access to another course.

## Embed a protected video

Add a WordPress **Shortcode** block to the MasterStudy lesson or course content:

```text
[zjget-player]https://cdn.example.com/course/protected-video.mp4[/zjget-player]
```

- Use a complete URL beginning with `http://` or `https://`.
- Put only the raw URL inside the shortcode.
- Use one ZJGet player on the same page.
- Player files load from `www.zjget.com`.
- The player is replaced with a placeholder while editing or previewing content.

## License workflow

1. Store the request and require WordPress sign-in.
2. Read `YourProductID` and select the first course with an active MasterStudy user-course record.
3. Create the DRM-X user when necessary.
4. Call `CheckUserIsRevoked` and `GetUserDetails` for an existing DRM-X user.
5. Apply the configured RightsID mode.
6. Request the license and return it to ZJGet.

A purchase or enrollment that MasterStudy no longer considers active does not pass. Downloaded protected files must pass the same sign-in and course-level authorization check when requesting a license.

## Troubleshooting

| Problem | Check |
| --- | --- |
| Learner is asked to sign in again | Use the same HTTPS domain for WordPress and `licstore5.php`. |
| Browser reports an insecure form | Use HTTPS for WordPress and the integration URL. |
| Learner is unauthorized | Confirm the numeric MasterStudy course post ID and active user-course record. |
| Shortcode is displayed as text | Activate MasterStudy LMS and this plugin; use a Shortcode block. |
| Player reports an invalid URL | Put one complete HTTP or HTTPS URL inside the shortcode. |
| DRM-X cannot be reached | Enable PHP SOAP and verify credentials and service area. |

## Privacy and security

The plugin may send the WordPress username, email, display name, IP address, and DRM client/device request data to the selected DRM-X service during user creation or license delivery. Review applicable privacy requirements, use HTTPS, and never publish `WebServiceAuthStr` or real credentials.

## 中文简介

**DRM-X 5.0 MasterStudy LMS 集成插件**用于连接 WordPress、MasterStudy LMS、DRM-X 5.0 和 ZJGet 播放器。只有登录 WordPress 且具有当前有效 MasterStudy 用户课程记录的学员才能获取加密视频许可证。

主要功能：

- 根据 DRM-X 许可证模板中的课程文章 ID 检查 MasterStudy 课程权限。
- 支持 `123-124-208` 格式的多课程 ID。
- DRM-X 用户不存在时自动创建，被吊销或锁定时拒绝许可证。
- 支持中国版、国际版和两种 RightsID 模式。
- 使用 `[zjget-player]URL[/zjget-player]` 嵌入加密视频。

安装后进入 **设置 > DRM-X 5.0**，填写 `AdminEmail`、`WebServiceAuthStr`、`GroupID`、服务区域和 RightsID 模式。在 DRM-X 5.0 许可证模板的 **Your Product ID** 中填写 MasterStudy 课程文章 ID。

## Support

- [DRM-X 5.0 product overview](https://www.drm-x.com/en)
- [DRM-X 5.0 features](https://www.drm-x.com/en/products/drm-x-5.0/features)
- [Haihaisoft](https://www.haihaisoft.com/)

Developed and maintained by [Haihaisoft](https://www.haihaisoft.com/).
