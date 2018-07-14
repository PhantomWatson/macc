<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
			'MembershipLevels' => \App\Model\Table\MembershipLevelsTable::class,
			'MembershipRenewalLogs' => \App\Model\Table\MembershipRenewalLogsTable::class,
			'Memberships' => \App\Model\Table\MembershipsTable::class,
			'Payments' => \App\Model\Table\PaymentsTable::class,
			'Pictures' => \App\Model\Table\PicturesTable::class,
			'Tags' => \App\Model\Table\TagsTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
		])
	);

	override(
		\Cake\ORM\Locator\LocatorInterface::get(0),
		map([
			'MembershipLevels' => \App\Model\Table\MembershipLevelsTable::class,
			'MembershipRenewalLogs' => \App\Model\Table\MembershipRenewalLogsTable::class,
			'Memberships' => \App\Model\Table\MembershipsTable::class,
			'Payments' => \App\Model\Table\PaymentsTable::class,
			'Pictures' => \App\Model\Table\PicturesTable::class,
			'Tags' => \App\Model\Table\TagsTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
		])
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
			'MembershipLevels' => \App\Model\Table\MembershipLevelsTable::class,
			'MembershipRenewalLogs' => \App\Model\Table\MembershipRenewalLogsTable::class,
			'Memberships' => \App\Model\Table\MembershipsTable::class,
			'Payments' => \App\Model\Table\PaymentsTable::class,
			'Pictures' => \App\Model\Table\PicturesTable::class,
			'Tags' => \App\Model\Table\TagsTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
		])
	);

	override(
		\ModelAwareTrait::loadModel(0),
		map([
			'MembershipLevels' => \App\Model\Table\MembershipLevelsTable::class,
			'MembershipRenewalLogs' => \App\Model\Table\MembershipRenewalLogsTable::class,
			'Memberships' => \App\Model\Table\MembershipsTable::class,
			'Payments' => \App\Model\Table\PaymentsTable::class,
			'Pictures' => \App\Model\Table\PicturesTable::class,
			'Tags' => \App\Model\Table\TagsTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
		])
	);

	override(
		\Cake\ORM\Table::addBehavior(0),
		map([
			'CounterCache' => \Cake\ORM\Table::class,
			'Timestamp' => \Cake\ORM\Table::class,
			'Translate' => \Cake\ORM\Table::class,
			'Tree' => \Cake\ORM\Table::class,
			'Josegonzalez/Upload.Upload' => \Cake\ORM\Table::class,
			'Xety/Cake3Sluggable.Sluggable' => \Cake\ORM\Table::class,
		])
	);

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'Auth' => \Cake\Controller\Component\AuthComponent::class,
			'Cookie' => \Cake\Controller\Component\CookieComponent::class,
			'Csrf' => \Cake\Controller\Component\CsrfComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'Paginator' => \Cake\Controller\Component\PaginatorComponent::class,
			'RequestHandler' => \Cake\Controller\Component\RequestHandlerComponent::class,
			'Security' => \Cake\Controller\Component\SecurityComponent::class,
			'Recaptcha.Recaptcha' => \Recaptcha\Controller\Component\RecaptchaComponent::class,
		])
	);

	override(
		\Cake\View\View::loadHelper(0),
		map([
			'Breadcrumbs' => \Cake\View\Helper\BreadcrumbsHelper::class,
			'Flash' => \Cake\View\Helper\FlashHelper::class,
			'Form' => \Cake\View\Helper\FormHelper::class,
			'Html' => \Cake\View\Helper\HtmlHelper::class,
			'Number' => \Cake\View\Helper\NumberHelper::class,
			'Paginator' => \Cake\View\Helper\PaginatorHelper::class,
			'Rss' => \Cake\View\Helper\RssHelper::class,
			'Session' => \Cake\View\Helper\SessionHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
			'Tag' => \App\View\Helper\TagHelper::class,
			'Bake.Bake' => \Bake\View\Helper\BakeHelper::class,
			'Bake.DocBlock' => \Bake\View\Helper\DocBlockHelper::class,
			'Migrations.Migration' => \Migrations\View\Helper\MigrationHelper::class,
			'Recaptcha.Recaptcha' => \Recaptcha\View\Helper\RecaptchaHelper::class,
		])
	);

	override(
		\Cake\ORM\Table::belongsTo(0),
		map([
			'MembershipLevels' => \Cake\ORM\Association\BelongsTo::class,
			'MembershipRenewalLogs' => \Cake\ORM\Association\BelongsTo::class,
			'Memberships' => \Cake\ORM\Association\BelongsTo::class,
			'Payments' => \Cake\ORM\Association\BelongsTo::class,
			'Pictures' => \Cake\ORM\Association\BelongsTo::class,
			'Tags' => \Cake\ORM\Association\BelongsTo::class,
			'Users' => \Cake\ORM\Association\BelongsTo::class,
		])
	);

	override(
		\Cake\ORM\Table::hasOne(0),
		map([
			'MembershipLevels' => \Cake\ORM\Association\HasOne::class,
			'MembershipRenewalLogs' => \Cake\ORM\Association\HasOne::class,
			'Memberships' => \Cake\ORM\Association\HasOne::class,
			'Payments' => \Cake\ORM\Association\HasOne::class,
			'Pictures' => \Cake\ORM\Association\HasOne::class,
			'Tags' => \Cake\ORM\Association\HasOne::class,
			'Users' => \Cake\ORM\Association\HasOne::class,
		])
	);

	override(
		\Cake\ORM\Table::hasMany(0),
		map([
			'MembershipLevels' => \Cake\ORM\Association\HasMany::class,
			'MembershipRenewalLogs' => \Cake\ORM\Association\HasMany::class,
			'Memberships' => \Cake\ORM\Association\HasMany::class,
			'Payments' => \Cake\ORM\Association\HasMany::class,
			'Pictures' => \Cake\ORM\Association\HasMany::class,
			'Tags' => \Cake\ORM\Association\HasMany::class,
			'Users' => \Cake\ORM\Association\HasMany::class,
		])
	);

	override(
		\Cake\ORM\Table::belongToMany(0),
		map([
			'MembershipLevels' => \Cake\ORM\Association\BelongsToMany::class,
			'MembershipRenewalLogs' => \Cake\ORM\Association\BelongsToMany::class,
			'Memberships' => \Cake\ORM\Association\BelongsToMany::class,
			'Payments' => \Cake\ORM\Association\BelongsToMany::class,
			'Pictures' => \Cake\ORM\Association\BelongsToMany::class,
			'Tags' => \Cake\ORM\Association\BelongsToMany::class,
			'Users' => \Cake\ORM\Association\BelongsToMany::class,
		])
	);

	override(
		\Cake\ORM\Table::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\ORM\Association::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\Database\Type::build(0),
		map([
			'tinyinteger' => \Cake\Database\Type\IntegerType::class,
			'smallinteger' => \Cake\Database\Type\IntegerType::class,
			'integer' => \Cake\Database\Type\IntegerType::class,
			'biginteger' => \Cake\Database\Type\IntegerType::class,
			'binary' => \Cake\Database\Type\BinaryType::class,
			'binaryuuid' => \Cake\Database\Type\BinaryUuidType::class,
			'boolean' => \Cake\Database\Type\BoolType::class,
			'date' => \Cake\Database\Type\DateType::class,
			'datetime' => \Cake\Database\Type\DateTimeType::class,
			'decimal' => \Cake\Database\Type\DecimalType::class,
			'float' => \Cake\Database\Type\FloatType::class,
			'json' => \Cake\Database\Type\JsonType::class,
			'string' => \Cake\Database\Type\StringType::class,
			'text' => \Cake\Database\Type\StringType::class,
			'time' => \Cake\Database\Type\TimeType::class,
			'timestamp' => \Cake\Database\Type\DateTimeType::class,
			'uuid' => \Cake\Database\Type\UuidType::class,
		])
	);

	override(
		\Cake\View\View::element(0),
		map([
			'commonmark_parsed' => \Cake\View\View::class,
			'favicons' => \Cake\View\View::class,
			'Flash/default' => \Cake\View\View::class,
			'Flash/error' => \Cake\View\View::class,
			'Flash/success' => \Cake\View\View::class,
			'header_nav' => \Cake\View\View::class,
			'jquery_ui' => \Cake\View\View::class,
			'pagination' => \Cake\View\View::class,
			'Tags/editor' => \Cake\View\View::class,
		])
	);

}
