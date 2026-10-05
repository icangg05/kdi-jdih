// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for Chinese (`zh`).
class AppLocalizationsZh extends AppLocalizations {
  AppLocalizationsZh([String locale = 'zh']) : super(locale);

  @override
  String get appName => '肯达里市 JDIH';

  @override
  String get navHome => '首页';

  @override
  String get navDocuments => '文件';

  @override
  String get navAskAi => 'AI 问答';

  @override
  String get navNews => '资讯';

  @override
  String get navMenu => '菜单';

  @override
  String aiAndSearch(String label) {
    return '$label与搜索';
  }

  @override
  String get errServer => '服务器出现问题，请稍后再试。';

  @override
  String get errUnknownReply => '服务器返回了无法识别的响应。如果使用公共 Wi-Fi，请确认已登录该网络。';

  @override
  String get errTooMany => '短时间内请求过多，请稍候再试。';

  @override
  String get errInvalidInput => '填写内容不完整或不正确，请检查后重新提交。';

  @override
  String get errNotFound => '未找到所请求的数据。';

  @override
  String errRequestFailed(int code) {
    return '请求无法处理（$code），请重试。';
  }

  @override
  String get errTimeout => '服务器响应缓慢，请重试。';

  @override
  String get errOffline => '无法连接到 JDIH 服务器，请检查网络连接后重试。';

  @override
  String minRead(int n) {
    return '阅读约 $n 分钟';
  }

  @override
  String get linkOpenFailed => '无法打开链接。';

  @override
  String get closeImage => '关闭图片';

  @override
  String get statusInForce => '有效';

  @override
  String get statusNotInForce => '失效';

  @override
  String get statusRevoked => '已废止';

  @override
  String get statusAmended => '已修订';

  @override
  String get legalDocument => '法规文件';

  @override
  String get seeAll => '查看全部';

  @override
  String get language => '语言';

  @override
  String get notFound => '未找到结果';

  @override
  String noMatch(String query) {
    return '没有与“$query”匹配的结果。';
  }

  @override
  String get noMatchHint => '请尝试更短的关键词、检查拼写或更换类别。';

  @override
  String get aiPromoBody => '用日常语言提问，以对话形式作答，并附上相关法律文件。';

  @override
  String get aiPromoStart => '开始提问';

  @override
  String get latest => '最新';

  @override
  String get share => '分享';

  @override
  String get readLess => '收起';

  @override
  String get readMore => '展开全文';

  @override
  String get retry => '重试';

  @override
  String get noData => '暂无数据。';

  @override
  String get emptyTitle => '暂无内容';

  @override
  String get reload => '重新加载';

  @override
  String get loadMore => '加载更多';

  @override
  String get catRegulations => '法规与决定';

  @override
  String get catRegulationsShort => '法规';

  @override
  String get catRegulationsSub => '地方条例、市长条例及市长决定';

  @override
  String get catMonographs => '法律专著';

  @override
  String get catMonographsShort => '专著';

  @override
  String get catMonographsSub => '地方法律书籍与研究';

  @override
  String get catArticles => '法律文章/期刊';

  @override
  String get catArticlesShort => '文章';

  @override
  String get catArticlesSub => '法律文章与杂志';

  @override
  String get catRulings => '法院判决';

  @override
  String get catRulingsShort => '判决';

  @override
  String get catRulingsSub => '与本地区相关的法院判决';

  @override
  String get latestDocuments => '最新文件';

  @override
  String get jdihNews => 'JDIH 资讯';

  @override
  String get collectionStats => '馆藏统计';

  @override
  String get greetMorning => '早上好';

  @override
  String get greetMidday => '中午好';

  @override
  String get greetAfternoon => '下午好';

  @override
  String get greetEvening => '晚上好';

  @override
  String get logoJdihn => 'JDIHN 标志';

  @override
  String get homeHeadline => '今天想查找哪项法规？';

  @override
  String get adatMeaning => '尊重习俗者，必受人尊重';

  @override
  String get homeSearchHint => '标题、编号或问题…';

  @override
  String get search => '搜索';

  @override
  String get noAbbr => '编号';

  @override
  String get views => '次浏览';

  @override
  String get downloads => '次下载';

  @override
  String get pickDocument => '选择文件';

  @override
  String get pickDocumentHint => '点击列表中的文件，即可在此处与其他结果并排查看详情。';

  @override
  String get legalDocuments => '法律文件';

  @override
  String get latestLegalProducts => '最新法律文件';

  @override
  String docsAvailable(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '共 $count 份文件',
    );
    return '$_temp0';
  }

  @override
  String get filterAllTypes => '所有类型';

  @override
  String get filterAllYears => '所有年份';

  @override
  String get filterAllStatus => '所有状态';

  @override
  String get type => '类型';

  @override
  String get year => '年份';

  @override
  String get status => '状态';

  @override
  String get number => '编号';

  @override
  String get searchTitleHint => '搜索标题…';

  @override
  String get reset => '重置';

  @override
  String docCount(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count 份文件',
    );
    return '$_temp0';
  }

  @override
  String docCountFiltered(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count 份文件符合筛选条件',
    );
    return '$_temp0';
  }

  @override
  String get emptyCategory => '该类别暂无文件。';

  @override
  String get loosenFilters => '请放宽筛选条件或检查关键词拼写。';

  @override
  String get resetFilter => '重置筛选';

  @override
  String get back => '返回';

  @override
  String numberValue(String nomor) {
    return '第 $nomor 号';
  }

  @override
  String yearValue(String tahun) {
    return '$tahun 年';
  }

  @override
  String get tabAbout => '简介';

  @override
  String tabFiles(int count) {
    return '附件（$count）';
  }

  @override
  String get tabRelated => '相关';

  @override
  String get tabImplementing => '实施法规';

  @override
  String get aboutDocument => '文件简介';

  @override
  String get abstract => '摘要';

  @override
  String abstractOf(String judul) {
    return '$judul摘要';
  }

  @override
  String fileAbstract(String judul) {
    return '摘要 $judul';
  }

  @override
  String get metaForm => '形式';

  @override
  String get metaPlacePublished => '出版地';

  @override
  String get publisher => '出版者';

  @override
  String get metaDateEnacted => '制定日期';

  @override
  String get metaDatePromulgated => '公布日期';

  @override
  String get source => '来源';

  @override
  String get metaLegalField => '法律领域';

  @override
  String get metaSignatory => '签署人';

  @override
  String get metaPhysicalDesc => '载体形态';

  @override
  String get metaCourt => '审判机构';

  @override
  String get metaPetitioner => '申请人';

  @override
  String get metaRespondent => '被申请人';

  @override
  String get metaCaseType => '案件类型';

  @override
  String get metaTeu => '主款目';

  @override
  String get subject => '主题';

  @override
  String get author => '作者';

  @override
  String get noFiles => '暂无附件';

  @override
  String get noFilesHint => '此文件暂无可查看或下载的附件，请查看相关法规。';

  @override
  String get document => '文件';

  @override
  String fileAttachmentN(String judul, int n) {
    return '$judul 附件 $n';
  }

  @override
  String get noRelated => '无相关法规';

  @override
  String get noRelatedHint => '此文件独立存在：既未修改或废止其他法规，也未被其他法规修改。';

  @override
  String get noImplementing => '暂无实施法规';

  @override
  String get noImplementingHint => '为实施本法规而发布的下位法规将显示在此处。';

  @override
  String get statistics => '统计';

  @override
  String viewsDownloads(String views, String downloads) {
    return '浏览 $views · 下载 $downloads';
  }

  @override
  String get fileUnavailable => '文件不可用';

  @override
  String get viewDocument => '查看文件';

  @override
  String get downloadDocument => '下载文件';

  @override
  String get fileKindPdf => 'PDF 文件 · 可在应用内阅读';

  @override
  String fileKindOther(String ext) {
    return '$ext 文件 · 下载后打开';
  }

  @override
  String get fileGeneric => '文件';

  @override
  String downloading(String name) {
    return '正在下载 $name…';
  }

  @override
  String get downloadFailed => '文件下载失败，请检查网络连接和存储空间后重试。';

  @override
  String get goToPage => '跳转到页面';

  @override
  String get pageNumber => '页码';

  @override
  String get cancel => '取消';

  @override
  String get go => '跳转';

  @override
  String pageOutOfRange(int total) {
    return '此文件仅有第 1 至 $total 页。';
  }

  @override
  String get download => '下载';

  @override
  String get pdfCannotDisplay => '此文件无法在应用内显示，但仍可下载并用 PDF 阅读器打开。';

  @override
  String get downloadInstead => '直接下载';

  @override
  String get prevPage => '上一页';

  @override
  String get nextPage => '下一页';

  @override
  String pageOf(int page, int total) {
    return '第 $page 页，共 $total 页';
  }

  @override
  String get view => '查看';

  @override
  String get newsAndInfo => '资讯与信息';

  @override
  String get news => '新闻';

  @override
  String get announcements => '公告';

  @override
  String get announcement => '公告';

  @override
  String get video => '视频';

  @override
  String get legalInfoShort => '法律信息';

  @override
  String get legalInfo => '法律信息';

  @override
  String get clearSearch => '清除搜索';

  @override
  String get noMatchHintShort => '请尝试更短的关键词或检查拼写。';

  @override
  String get searchNewsHint => '搜索新闻标题…';

  @override
  String get emptyNews => '暂无已发布的新闻。';

  @override
  String get searchAnnouncementsHint => '搜索公告标题…';

  @override
  String get emptyAnnouncements => '暂无公告。';

  @override
  String get emptyVideos => 'JDIH 频道暂无视频。';

  @override
  String get moreNews => '更多新闻';

  @override
  String get moreAnnouncements => '更多公告';

  @override
  String get moreVideos => '更多视频';

  @override
  String get enlargeCover => '放大封面图片';

  @override
  String get tapToEnlarge => '点击放大';

  @override
  String get attachment => '附件';

  @override
  String get announcementAttachment => '公告附件';

  @override
  String fileAttachmentOf(String judul) {
    return '附件 $judul';
  }

  @override
  String fileDocumentOf(String judul) {
    return '文件 $judul';
  }

  @override
  String get all => '全部';

  @override
  String get emptyLegalInfo => '学术文本、法规草案和法律研究发布后将显示在此处。';

  @override
  String get emptyCategoryTitle => '该类别暂无内容';

  @override
  String get emptySelectedCategory => '所选类别暂无文件。';

  @override
  String emptyTypeNamed(String jenis) {
    return '暂无“$jenis”类文件，其他类别可能已有内容。';
  }

  @override
  String get seeAllCategories => '查看所有类别';

  @override
  String get linkCopied => '视频链接已复制。';

  @override
  String get videoNotPlayable => '此视频存放在 YouTube 以外的平台，暂无法在应用内播放。';

  @override
  String get openInYoutube => '在 YouTube 中打开';

  @override
  String get openVideoLink => '打开视频链接';

  @override
  String get copy => '复制';

  @override
  String get profileHistory => '历史沿革';

  @override
  String get profileLegalBasis => '法律依据';

  @override
  String get profileVision => '愿景';

  @override
  String get profileMission => '使命';

  @override
  String get profileStructure => '组织架构';

  @override
  String get more => '更多';

  @override
  String get jdihProfile => 'JDIH 简介';

  @override
  String get disabilityServices => '残障服务';

  @override
  String get lawMaking => '法规制定';

  @override
  String get satisfactionSurvey => '满意度调查';

  @override
  String get aboutJdih => '关于 JDIH';

  @override
  String get menuServices => '服务与信息';

  @override
  String get menuApp => '应用';

  @override
  String get orgStructureOnWeb => '组织架构内容请访问肯达里市 JDIH 网站。';

  @override
  String get jdihFull => '法律文献与信息网络';

  @override
  String get phone => '电话';

  @override
  String get email => '电子邮件';

  @override
  String get address => '地址';

  @override
  String get socialMedia => '社交媒体';

  @override
  String get appLabel => '肯达里市 JDIH 应用';

  @override
  String loadingApp(String app) {
    return '$app，正在加载';
  }

  @override
  String get kendariGov => '肯达里市政府';

  @override
  String get puuAcademicPaper => '学术文本';

  @override
  String get puuExplanatory => '说明文本';

  @override
  String get puuDraft => '法规草案';

  @override
  String get puuResearch => '法律研究';

  @override
  String get puuLegalReview => '法律评估';

  @override
  String get puuConstitutionalReview => '宪法评估';

  @override
  String get puuAnalysis => '分析与评估';

  @override
  String get secBackground => '背景';

  @override
  String get secProblem => '问题陈述';

  @override
  String get secObjective => '研究目的';

  @override
  String get secMethod => '研究方法';

  @override
  String get secFocus => '研究重点';

  @override
  String get secResults => '研究结果';

  @override
  String get secReviewObject => '评估对象';

  @override
  String get secReviewConclusion => '评估结论';

  @override
  String get secConstitutional => '宪法层面';

  @override
  String get secEvalFindings => '评估发现';

  @override
  String get secRecommendation => '建议';

  @override
  String get secImprovement => '改进建议';

  @override
  String get secNotes => '备注';

  @override
  String get metaInitiator => '发起机构';

  @override
  String get metaStage => '阶段';

  @override
  String get metaWriter => '撰写人';

  @override
  String get metaEditor => '编辑';

  @override
  String get metaKeywords => '关键词';

  @override
  String get metaViews => '浏览次数';

  @override
  String get mainDocument => '主文件';

  @override
  String timesCount(int n) {
    return '$n 次';
  }

  @override
  String get searchDisabilityHint => '搜索残障相关文件…';

  @override
  String get metaPlaceEnacted => '制定地点';

  @override
  String get metaEnactedBy => '制定机关';

  @override
  String get metaDisabilityType => '残障类型';

  @override
  String get metaScope => '适用范围';

  @override
  String get metaPolicySector => '政策领域';

  @override
  String get metaPages => '页数';

  @override
  String get smartSearch => '智能搜索';

  @override
  String get textSearch => '文本搜索';

  @override
  String get searchDocsHint => '搜索标题、编号或主题…';

  @override
  String resultsFor(int count, String query) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '“$query”共 $count 条结果',
    );
    return '$_temp0';
  }

  @override
  String get noMatchHintAi => '请尝试更短的关键词、更换上方类别，或直接询问 AI。';

  @override
  String get searchLegalProducts => '搜索法律文件';

  @override
  String get searchIntro => '输入法规标题、编号或主题，然后按键盘上的搜索键。上方类别可缩小结果范围。';

  @override
  String get aiSuggest1 => '垃圾处理费';

  @override
  String get aiSuggest1Ask => '关于垃圾处理费有哪些规定？';

  @override
  String get aiSuggest2 => '最新市长条例';

  @override
  String get aiSuggest2Ask => '最新的市长条例是关于什么的？';

  @override
  String get aiSuggest3 => '办理建筑许可';

  @override
  String get aiSuggest3Ask => '如何办理建筑许可（IMB/PBG）？';

  @override
  String get aiSuggest4 => '地方税';

  @override
  String get aiSuggest4Ask => '肯达里市有哪些地方税种？';

  @override
  String get aiSuggest5 => '地方预算 (APBD)';

  @override
  String get aiSuggest5Ask => '最新的地方预算（APBD）条例包含哪些内容？';

  @override
  String get aiSuggest6 => '残障人士权利';

  @override
  String get aiSuggest6Ask => '关于残障人士权利有哪些规定？';

  @override
  String get aiNoAnswer => '抱歉，我暂时无法回答这个问题。';

  @override
  String get chatCleared => '对话已清除。';

  @override
  String get undo => '撤销';

  @override
  String get clearChat => '清除对话';

  @override
  String chatLimitReached(int max) {
    return '已达 $max 个问题的上限';
  }

  @override
  String get askHint => '输入您的问题…';

  @override
  String get send => '发送';

  @override
  String get aiDisclaimer => '回答由 AI 自动生成，请务必核对原始文件。';

  @override
  String get jdihAssistant => 'JDIH 助手';

  @override
  String get aiGreeting => '您好！欢迎咨询肯达里市的法律法规问题。我会为您解答，并列出相关的 JDIH 文件。';

  @override
  String get tryAsking => '试着问问';

  @override
  String get resend => '重新发送';

  @override
  String get aiStepUnderstand => '理解问题';

  @override
  String get aiStepSearch => '检索 JDIH 文件';

  @override
  String get aiStepMatch => '匹配相关条款';

  @override
  String get aiStepCompose => '撰写回答';

  @override
  String get aiSearching => '正在检索文件';

  @override
  String get relatedDocs => '相关文件';

  @override
  String docsAttached(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '附 $count 份文件',
    );
    return '$_temp0';
  }

  @override
  String percentRelevant(int pct) {
    return '相关度 $pct%';
  }

  @override
  String chatFull(int max) {
    return '本次对话已达 $max 个问题。请开始新对话以继续提问。';
  }

  @override
  String get newChat => '新对话';

  @override
  String updatedAt(String date) {
    return '更新于 $date。';
  }

  @override
  String get statsSource => '数据直接取自肯达里市 JDIH 数据库。';

  @override
  String get totalLegalDocs => '法律文件总数';

  @override
  String get metaDownloads => '下载次数';

  @override
  String get docsPerYear => '按年份统计';

  @override
  String get docsPerYearSub => '按发布年份统计的文件数量';

  @override
  String labelDocs(String label, int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString 份文件',
    );
    return '$label：$_temp0';
  }

  @override
  String otherTypes(int n) {
    return '其他（$n 类）';
  }

  @override
  String get docsPerType => '按类型统计';

  @override
  String get docsPerTypeSub => '馆藏最多的七个类型';

  @override
  String get statusTitle => '效力状态';

  @override
  String get statusSub => '仍然有效的馆藏比例';

  @override
  String get statusNoData => '暂无状态数据。';

  @override
  String labelDocsPct(String label, int count, String pct) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString 份文件',
    );
    return '$label：$_temp0，$pct';
  }

  @override
  String get mostViewed => '最多浏览';

  @override
  String get mostViewedSub => '阅读量最高的五份文件';

  @override
  String get mostViewedEmpty => '尚未记录任何文件阅读。访客打开文件页面后将开始统计。';

  @override
  String viewedTimes(int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '浏览 $countString 次',
    );
    return '$_temp0';
  }

  @override
  String get userStudent => '学生';

  @override
  String get userAcademic => '学者';

  @override
  String get userPractitioner => '法律从业者';

  @override
  String get userPublic => '普通公众';

  @override
  String get userOther => '其他';

  @override
  String get aspectAccess => '访问便捷性';

  @override
  String get aspectCompleteness => '信息完整性';

  @override
  String get aspectSpeed => '加载速度';

  @override
  String get aspectInterface => '界面设计';

  @override
  String get aspectRelevance => '搜索相关性';

  @override
  String get errNameRequired => '请填写姓名。';

  @override
  String get errEmailFormat => '电子邮件格式不正确，例如：name@example.com';

  @override
  String get errPickUserType => '请选择一种用户类型。';

  @override
  String get errRateAll => '请为每一项打分。';

  @override
  String get thankYou => '谢谢！';

  @override
  String get surveySent => '您的问卷已提交。';

  @override
  String get ok => '好';

  @override
  String get fieldName => '姓名';

  @override
  String get fieldInstitution => '单位';

  @override
  String get fieldUserType => '用户类型';

  @override
  String get fieldRating => '服务评价';

  @override
  String get fieldSuggestions => '改进建议';

  @override
  String get fieldFeatures => '期望的功能';

  @override
  String get fieldContactOk => '愿意接受联系';

  @override
  String get fieldContact => '联系方式（手机/WhatsApp）';

  @override
  String get submitSurvey => '提交问卷';

  @override
  String get surveyHeading => '帮助我们做得更好';

  @override
  String get surveyIntro => '您的评价将帮助我们改进 JDIH 服务。标有“选填”的项目可以跳过。';

  @override
  String get surveyAbout => '关于您';

  @override
  String get surveyFeedback => '意见与建议';

  @override
  String get optional => '选填';

  @override
  String ratingWord(String n) {
    String _temp0 = intl.Intl.selectLogic(n, {
      '1': '很差',
      '2': '较差',
      '3': '一般',
      '4': '好',
      'other': '很好',
    });
    return '$_temp0';
  }

  @override
  String surveyProgress(int done, int total) {
    return '必填项 $done/$total';
  }

  @override
  String ratingTooltip(String label, int n) {
    return '$label，$n/5';
  }
}
