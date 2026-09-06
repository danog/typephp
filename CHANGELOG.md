# TypePHP change log

## 0.8.0

### Breaking: compile-time API namespace cleanup

TypePHP compile-time APIs now occupy only two global class symbols:

- `Type::*` describes types used by extension-method metadata. It does not
  execute conversions or other built-in operations.
- `std::*` contains TypePHP built-in compile-time functions.

As with PHP class and method names, the `std` / `Type` class names and `std`
method names are case-insensitive. `Type::Int` and the other `Type` members are
class constants, so their constant names remain case-sensitive under PHP rules.

The former global compile-time functions have changed as follows:

| Before 0.8 | Since 0.8 | Notes |
| --- | --- | --- |
| `any($value)` | `std::any($value)` | Erases the static type to `mixed/any`. |
| `refval($target)` | `std::ref($target)` | Explicit reference wrapper for call arguments. |
| `expected($condition)` | `std::expected($condition)` | Emits the `EXPECTED(...)` branch hint. |
| `unexpected($condition)` | `std::unexpected($condition)` | Emits the `UNEXPECTED(...)` branch hint. |
| `objval($value, Foo::class)` | `$value->toObject(Foo::class)` | Replaced by the existing keyword method. |

No compatibility functions are installed in the global namespace. Applications
may define and call their own `any()`, `refval()`, `expected()`, `unexpected()`,
and `objval()` functions without being intercepted by the compiler.

`std::ref()` retains the previous reference-wrapper restrictions: its target
must be a variable, array element, or object property, and it is only valid as a
call argument wrapper. `toRef()` remains the equivalent keyword method.

All projects using the old spellings must update their source and rebuild.

### Compatibility policy before 1.0

TypePHP is still pre-1.0 software. Public compiler APIs, TypePHP-specific source
syntax, generated-code interfaces, configuration, and packaging may change
between minor releases. Breaking changes will be documented here, but users
should review the change log and run their full test suite before upgrading.

---

## 0.8.0（中文）

### 破坏性变更：整理编译期 API 命名空间

TypePHP 编译期 API 现在只占用两个全局类符号：

- `Type::*` 仅描述扩展方法元数据等场景使用的类型，不执行类型转换或其他内置操作。
- `std::*` 承载 TypePHP 内置编译期函数。

遵循 PHP 的类名与方法名规则，`std` / `Type` 类名以及 `std` 方法名均不区分
大小写。`Type::Int` 等成员属于类常量，因此其常量名仍按 PHP 规则区分大小写。

原全局编译期函数迁移如下：

| 0.8 之前 | 0.8 起 | 说明 |
| --- | --- | --- |
| `any($value)` | `std::any($value)` | 将静态类型降级为 `mixed/any`。 |
| `refval($target)` | `std::ref($target)` | 调用参数的显式引用包装器。 |
| `expected($condition)` | `std::expected($condition)` | 生成 `EXPECTED(...)` 分支提示。 |
| `unexpected($condition)` | `std::unexpected($condition)` | 生成 `UNEXPECTED(...)` 分支提示。 |
| `objval($value, Foo::class)` | `$value->toObject(Foo::class)` | 改用现有关键词方法。 |

TypePHP 不在全局命名空间安装兼容函数。应用可以自行定义并正常调用
`any()`、`refval()`、`expected()`、`unexpected()` 和 `objval()`，编译器不会拦截。

`std::ref()` 延续原引用包装限制：目标必须是变量、数组元素或对象属性，且只能作为
调用参数包装器使用。`toRef()` 仍是等价的关键词方法。

使用旧写法的项目必须修改源码并重新编译。

### 1.0 之前的兼容性策略

TypePHP 目前仍处于 1.0 之前。公开编译器 API、TypePHP 专有源码接口、生成代码接口、
配置和打包方式都可能在次版本中发生变化。破坏性变更会记录在本文件中；升级前请阅读
变更记录，并运行项目的完整测试。
