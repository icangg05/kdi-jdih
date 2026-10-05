// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for Korean (`ko`).
class AppLocalizationsKo extends AppLocalizations {
  AppLocalizationsKo([String locale = 'ko']) : super(locale);

  @override
  String get appName => '켄다리시 JDIH';

  @override
  String get navHome => '홈';

  @override
  String get navDocuments => '문서';

  @override
  String get navAskAi => 'AI 질문';

  @override
  String get navNews => '소식';

  @override
  String get navMenu => '메뉴';

  @override
  String aiAndSearch(String label) {
    return '$label 및 검색';
  }

  @override
  String get errServer => '서버에 문제가 있습니다. 잠시 후 다시 시도해 주세요.';

  @override
  String get errUnknownReply =>
      '서버가 알 수 없는 응답을 보냈습니다. 공용 Wi-Fi를 사용 중이라면 네트워크에 로그인했는지 확인해 주세요.';

  @override
  String get errTooMany => '짧은 시간에 요청이 너무 많습니다. 잠시 기다린 후 다시 시도해 주세요.';

  @override
  String get errInvalidInput => '입력 내용이 불완전하거나 올바르지 않습니다. 확인 후 다시 보내 주세요.';

  @override
  String get errNotFound => '요청한 데이터를 찾을 수 없습니다.';

  @override
  String errRequestFailed(int code) {
    return '요청을 처리할 수 없습니다($code). 다시 시도해 주세요.';
  }

  @override
  String get errTimeout => '서버 응답이 느립니다. 다시 시도해 주세요.';

  @override
  String get errOffline => 'JDIH 서버에 연결할 수 없습니다. 인터넷 연결을 확인한 후 다시 시도해 주세요.';

  @override
  String minRead(int n) {
    return '$n분 분량';
  }

  @override
  String get linkOpenFailed => '링크를 열 수 없습니다.';

  @override
  String get closeImage => '이미지 닫기';

  @override
  String get statusInForce => '시행 중';

  @override
  String get statusNotInForce => '효력 상실';

  @override
  String get statusRevoked => '폐지됨';

  @override
  String get statusAmended => '개정됨';

  @override
  String get legalDocument => '규정 문서';

  @override
  String get seeAll => '모두 보기';

  @override
  String get language => '언어';

  @override
  String get notFound => '결과 없음';

  @override
  String noMatch(String query) {
    return '\"$query\"에 맞는 결과가 없습니다.';
  }

  @override
  String get noMatchHint => '더 짧은 검색어를 쓰거나 철자를 확인하거나 카테고리를 바꿔 보세요.';

  @override
  String get aiPromoBody => '일상적인 말로 질문하세요. 관련 법률 문서와 함께 대화로 답해 드립니다.';

  @override
  String get aiPromoStart => '질문하기';

  @override
  String get latest => '최신';

  @override
  String get share => '공유';

  @override
  String get readLess => '접기';

  @override
  String get readMore => '더 보기';

  @override
  String get retry => '다시 시도';

  @override
  String get noData => '데이터가 없습니다.';

  @override
  String get emptyTitle => '아직 내용이 없습니다';

  @override
  String get reload => '새로고침';

  @override
  String get loadMore => '더 불러오기';

  @override
  String get catRegulations => '규정 및 결정';

  @override
  String get catRegulationsShort => '규정';

  @override
  String get catRegulationsSub => '지방 조례, 시장 규칙 및 시장 결정';

  @override
  String get catMonographs => '법률 단행본';

  @override
  String get catMonographsShort => '단행본';

  @override
  String get catMonographsSub => '지역 법률 도서 및 연구';

  @override
  String get catArticles => '법률 기사/잡지';

  @override
  String get catArticlesShort => '기사';

  @override
  String get catArticlesSub => '법률 기사 및 잡지';

  @override
  String get catRulings => '판결';

  @override
  String get catRulingsShort => '판결';

  @override
  String get catRulingsSub => '지역 관련 법원 판결';

  @override
  String get latestDocuments => '최신 문서';

  @override
  String get jdihNews => 'JDIH 소식';

  @override
  String get collectionStats => '소장 자료 통계';

  @override
  String get greetMorning => '좋은 아침입니다';

  @override
  String get greetMidday => '안녕하세요';

  @override
  String get greetAfternoon => '좋은 오후입니다';

  @override
  String get greetEvening => '좋은 저녁입니다';

  @override
  String get logoJdihn => 'JDIHN 로고';

  @override
  String get homeHeadline => '오늘은 어떤 법규를 찾으시나요?';

  @override
  String get adatMeaning => '관습을 존중하는 자는 존중받는다';

  @override
  String get homeSearchHint => '제목, 번호 또는 질문...';

  @override
  String get search => '검색';

  @override
  String get noAbbr => '번호';

  @override
  String get views => '회 조회';

  @override
  String get downloads => '회 다운로드';

  @override
  String get pickDocument => '문서를 선택하세요';

  @override
  String get pickDocumentHint =>
      '목록에서 문서를 누르면 다른 결과와 나란히 여기에서 상세 내용을 볼 수 있습니다.';

  @override
  String get legalDocuments => '법률 문서';

  @override
  String get latestLegalProducts => '최신 법률 자료';

  @override
  String docsAvailable(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '문서 $count건',
    );
    return '$_temp0';
  }

  @override
  String get filterAllTypes => '모든 유형';

  @override
  String get filterAllYears => '모든 연도';

  @override
  String get filterAllStatus => '모든 상태';

  @override
  String get type => '유형';

  @override
  String get year => '연도';

  @override
  String get status => '상태';

  @override
  String get number => '번호';

  @override
  String get searchTitleHint => '제목 검색...';

  @override
  String get reset => '초기화';

  @override
  String docCount(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '문서 $count건',
    );
    return '$_temp0';
  }

  @override
  String docCountFiltered(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '필터에 맞는 문서 $count건',
    );
    return '$_temp0';
  }

  @override
  String get emptyCategory => '이 카테고리에는 아직 문서가 없습니다.';

  @override
  String get loosenFilters => '필터 조건을 줄이거나 검색어 철자를 확인해 보세요.';

  @override
  String get resetFilter => '필터 초기화';

  @override
  String get back => '뒤로';

  @override
  String numberValue(String nomor) {
    return '제$nomor호';
  }

  @override
  String yearValue(String tahun) {
    return '$tahun년';
  }

  @override
  String get tabAbout => '개요';

  @override
  String tabFiles(int count) {
    return '파일($count)';
  }

  @override
  String get tabRelated => '관련';

  @override
  String get tabImplementing => '시행 규정';

  @override
  String get aboutDocument => '문서 개요';

  @override
  String get abstract => '초록';

  @override
  String abstractOf(String judul) {
    return '$judul 초록';
  }

  @override
  String fileAbstract(String judul) {
    return '초록 $judul';
  }

  @override
  String get metaForm => '형식';

  @override
  String get metaPlacePublished => '발행지';

  @override
  String get publisher => '발행처';

  @override
  String get metaDateEnacted => '제정일';

  @override
  String get metaDatePromulgated => '공포일';

  @override
  String get source => '출처';

  @override
  String get metaLegalField => '법 분야';

  @override
  String get metaSignatory => '서명자';

  @override
  String get metaPhysicalDesc => '형태 사항';

  @override
  String get metaCourt => '법원';

  @override
  String get metaPetitioner => '신청인';

  @override
  String get metaRespondent => '피신청인';

  @override
  String get metaCaseType => '사건 유형';

  @override
  String get metaTeu => '주표목';

  @override
  String get subject => '주제';

  @override
  String get author => '저자';

  @override
  String get noFiles => '아직 파일이 없습니다';

  @override
  String get noFilesHint => '이 문서에는 아직 보거나 내려받을 파일이 없습니다. 관련 규정을 확인해 보세요.';

  @override
  String get document => '문서';

  @override
  String fileAttachmentN(String judul, int n) {
    return '$judul 첨부 $n';
  }

  @override
  String get noRelated => '관련 규정 없음';

  @override
  String get noRelatedHint =>
      '이 문서는 독립적입니다. 다른 규정을 개정하거나 폐지하지 않으며, 다른 규정에 의해 개정되지도 않았습니다.';

  @override
  String get noImplementing => '시행 규정 없음';

  @override
  String get noImplementingHint => '이 규정을 시행하기 위해 제정된 하위 규정이 여기에 표시됩니다.';

  @override
  String get statistics => '통계';

  @override
  String viewsDownloads(String views, String downloads) {
    return '조회 $views · 다운로드 $downloads';
  }

  @override
  String get fileUnavailable => '파일 없음';

  @override
  String get viewDocument => '문서 보기';

  @override
  String get downloadDocument => '문서 다운로드';

  @override
  String get fileKindPdf => 'PDF 문서 · 앱에서 읽기 가능';

  @override
  String fileKindOther(String ext) {
    return '$ext 파일 · 내려받아 열기';
  }

  @override
  String get fileGeneric => '파일';

  @override
  String downloading(String name) {
    return '$name 내려받는 중...';
  }

  @override
  String get downloadFailed =>
      '파일을 내려받지 못했습니다. 연결 상태와 저장 공간을 확인한 후 다시 시도해 주세요.';

  @override
  String get goToPage => '페이지 이동';

  @override
  String get pageNumber => '페이지 번호';

  @override
  String get cancel => '취소';

  @override
  String get go => '이동';

  @override
  String pageOutOfRange(int total) {
    return '이 문서는 1~$total페이지만 있습니다.';
  }

  @override
  String get download => '다운로드';

  @override
  String get pdfCannotDisplay => '이 문서는 앱에서 표시할 수 없지만 내려받아 PDF 뷰어로 열 수 있습니다.';

  @override
  String get downloadInstead => '내려받기';

  @override
  String get prevPage => '이전 페이지';

  @override
  String get nextPage => '다음 페이지';

  @override
  String pageOf(int page, int total) {
    return '$page / $total페이지';
  }

  @override
  String get view => '보기';

  @override
  String get newsAndInfo => '소식 및 정보';

  @override
  String get news => '뉴스';

  @override
  String get announcements => '공지사항';

  @override
  String get announcement => '공지';

  @override
  String get video => '동영상';

  @override
  String get legalInfoShort => '법률 정보';

  @override
  String get legalInfo => '법률 정보';

  @override
  String get clearSearch => '검색어 지우기';

  @override
  String get noMatchHintShort => '더 짧은 검색어를 쓰거나 철자를 확인해 보세요.';

  @override
  String get searchNewsHint => '뉴스 제목 검색...';

  @override
  String get emptyNews => '아직 게시된 뉴스가 없습니다.';

  @override
  String get searchAnnouncementsHint => '공지 제목 검색...';

  @override
  String get emptyAnnouncements => '아직 공지사항이 없습니다.';

  @override
  String get emptyVideos => 'JDIH 채널에 아직 동영상이 없습니다.';

  @override
  String get moreNews => '다른 뉴스';

  @override
  String get moreAnnouncements => '다른 공지사항';

  @override
  String get moreVideos => '다른 동영상';

  @override
  String get enlargeCover => '표지 이미지 확대';

  @override
  String get tapToEnlarge => '눌러서 확대';

  @override
  String get attachment => '첨부';

  @override
  String get announcementAttachment => '공지 첨부 파일';

  @override
  String fileAttachmentOf(String judul) {
    return '첨부 $judul';
  }

  @override
  String fileDocumentOf(String judul) {
    return '문서 $judul';
  }

  @override
  String get all => '전체';

  @override
  String get emptyLegalInfo => '학술 문서, 규정 초안 및 법률 연구가 게시되면 여기에 표시됩니다.';

  @override
  String get emptyCategoryTitle => '이 카테고리는 비어 있습니다';

  @override
  String get emptySelectedCategory => '선택한 카테고리에 아직 문서가 없습니다.';

  @override
  String emptyTypeNamed(String jenis) {
    return '아직 $jenis 문서가 없습니다. 다른 카테고리에는 있을 수 있습니다.';
  }

  @override
  String get seeAllCategories => '모든 카테고리 보기';

  @override
  String get linkCopied => '동영상 링크를 복사했습니다.';

  @override
  String get videoNotPlayable => '이 동영상은 YouTube 외부에 있어 아직 앱에서 재생할 수 없습니다.';

  @override
  String get openInYoutube => 'YouTube에서 열기';

  @override
  String get openVideoLink => '동영상 링크 열기';

  @override
  String get copy => '복사';

  @override
  String get profileHistory => '연혁';

  @override
  String get profileLegalBasis => '법적 근거';

  @override
  String get profileVision => '비전';

  @override
  String get profileMission => '사명';

  @override
  String get profileStructure => '조직 구조';

  @override
  String get more => '더보기';

  @override
  String get jdihProfile => 'JDIH 프로필';

  @override
  String get disabilityServices => '장애인 서비스';

  @override
  String get lawMaking => '법령 제정';

  @override
  String get satisfactionSurvey => '만족도 조사';

  @override
  String get aboutJdih => 'JDIH 소개';

  @override
  String get menuServices => '서비스 및 정보';

  @override
  String get menuApp => '앱';

  @override
  String get orgStructureOnWeb => '조직 구조는 켄다리시 JDIH 웹사이트에서 볼 수 있습니다.';

  @override
  String get jdihFull => '법률 문서 및 정보 네트워크';

  @override
  String get phone => '전화';

  @override
  String get email => '이메일';

  @override
  String get address => '주소';

  @override
  String get socialMedia => '소셜 미디어';

  @override
  String get appLabel => '켄다리시 JDIH 앱';

  @override
  String loadingApp(String app) {
    return '$app, 불러오는 중';
  }

  @override
  String get kendariGov => '켄다리시 정부';

  @override
  String get puuAcademicPaper => '학술 문서';

  @override
  String get puuExplanatory => '설명서';

  @override
  String get puuDraft => '법령 초안';

  @override
  String get puuResearch => '법률 연구';

  @override
  String get puuLegalReview => '법률 검토';

  @override
  String get puuConstitutionalReview => '헌법 검토';

  @override
  String get puuAnalysis => '분석 및 평가';

  @override
  String get secBackground => '배경';

  @override
  String get secProblem => '문제 제기';

  @override
  String get secObjective => '연구 목적';

  @override
  String get secMethod => '방법론';

  @override
  String get secFocus => '연구 초점';

  @override
  String get secResults => '연구 결과';

  @override
  String get secReviewObject => '검토 대상';

  @override
  String get secReviewConclusion => '검토 결론';

  @override
  String get secConstitutional => '헌법적 측면';

  @override
  String get secEvalFindings => '평가 결과';

  @override
  String get secRecommendation => '권고';

  @override
  String get secImprovement => '개선 권고';

  @override
  String get secNotes => '비고';

  @override
  String get metaInitiator => '발의 기관';

  @override
  String get metaStage => '단계';

  @override
  String get metaWriter => '작성자';

  @override
  String get metaEditor => '편집자';

  @override
  String get metaKeywords => '키워드';

  @override
  String get metaViews => '조회수';

  @override
  String get mainDocument => '본문 문서';

  @override
  String timesCount(int n) {
    return '$n회';
  }

  @override
  String get searchDisabilityHint => '장애 관련 문서 검색...';

  @override
  String get metaPlaceEnacted => '제정 장소';

  @override
  String get metaEnactedBy => '제정 기관';

  @override
  String get metaDisabilityType => '장애 유형';

  @override
  String get metaScope => '범위';

  @override
  String get metaPolicySector => '정책 분야';

  @override
  String get metaPages => '페이지 수';

  @override
  String get smartSearch => '스마트 검색';

  @override
  String get textSearch => '텍스트 검색';

  @override
  String get searchDocsHint => '제목, 번호 또는 주제 검색...';

  @override
  String resultsFor(int count, String query) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '\"$query\" 검색 결과 $count건',
    );
    return '$_temp0';
  }

  @override
  String get noMatchHintAi => '더 짧은 검색어를 쓰거나 위의 카테고리를 바꾸거나 AI에게 직접 물어보세요.';

  @override
  String get searchLegalProducts => '법률 자료 검색';

  @override
  String get searchIntro =>
      '규정 제목, 번호 또는 주제를 입력한 뒤 키보드의 검색 버튼을 누르세요. 위의 카테고리로 결과를 좁힐 수 있습니다.';

  @override
  String get aiSuggest1 => '쓰레기 수수료';

  @override
  String get aiSuggest1Ask => '쓰레기 처리 수수료에 관한 규정은 무엇인가요?';

  @override
  String get aiSuggest2 => '최신 시장 규칙';

  @override
  String get aiSuggest2Ask => '최신 시장 규칙은 무엇에 관한 것인가요?';

  @override
  String get aiSuggest3 => '건축 허가(IMB/PBG)';

  @override
  String get aiSuggest3Ask => '건축 허가(IMB/PBG)는 어떻게 신청하나요?';

  @override
  String get aiSuggest4 => '지방세';

  @override
  String get aiSuggest4Ask => '켄다리시의 지방세에는 어떤 종류가 있나요?';

  @override
  String get aiSuggest5 => '지방 예산(APBD)';

  @override
  String get aiSuggest5Ask => '최신 지방 예산(APBD) 조례에는 어떤 내용이 있나요?';

  @override
  String get aiSuggest6 => '장애인 권리';

  @override
  String get aiSuggest6Ask => '장애인 권리에 관한 규정은 무엇인가요?';

  @override
  String get aiNoAnswer => '죄송합니다. 아직 그 질문에는 답할 수 없습니다.';

  @override
  String get chatCleared => '대화를 지웠습니다.';

  @override
  String get undo => '실행 취소';

  @override
  String get clearChat => '대화 지우기';

  @override
  String chatLimitReached(int max) {
    return '질문 $max개 한도에 도달했습니다';
  }

  @override
  String get askHint => '질문을 입력하세요...';

  @override
  String get send => '보내기';

  @override
  String get aiDisclaimer => '답변은 AI가 자동으로 생성합니다. 반드시 원문 문서를 확인하세요.';

  @override
  String get jdihAssistant => 'JDIH 도우미';

  @override
  String get aiGreeting =>
      '안녕하세요! 켄다리시의 법률과 규정에 대해 무엇이든 물어보세요. 답변과 함께 관련 JDIH 문서를 알려 드립니다.';

  @override
  String get tryAsking => '이렇게 물어보세요';

  @override
  String get resend => '다시 보내기';

  @override
  String get aiStepUnderstand => '질문 이해 중';

  @override
  String get aiStepSearch => 'JDIH 문서 검색 중';

  @override
  String get aiStepMatch => '관련 조항 대조 중';

  @override
  String get aiStepCompose => '답변 작성 중';

  @override
  String get aiSearching => '문서 검색 중';

  @override
  String get relatedDocs => '관련 문서';

  @override
  String docsAttached(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '문서 $count건 첨부',
    );
    return '$_temp0';
  }

  @override
  String percentRelevant(int pct) {
    return '관련도 $pct%';
  }

  @override
  String chatFull(int max) {
    return '이 대화는 질문 $max개에 도달했습니다. 계속 질문하려면 새 대화를 시작하세요.';
  }

  @override
  String get newChat => '새 대화';

  @override
  String updatedAt(String date) {
    return '$date 업데이트.';
  }

  @override
  String get statsSource => '수치는 켄다리시 JDIH 데이터베이스에서 직접 집계합니다.';

  @override
  String get totalLegalDocs => '전체 법률 문서';

  @override
  String get metaDownloads => '다운로드 수';

  @override
  String get docsPerYear => '연도별 문서';

  @override
  String get docsPerYearSub => '발행 연도별 문서 수';

  @override
  String labelDocs(String label, int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '문서 $countString건',
    );
    return '$label: $_temp0';
  }

  @override
  String otherTypes(int n) {
    return '기타($n개 유형)';
  }

  @override
  String get docsPerType => '유형별 문서';

  @override
  String get docsPerTypeSub => '소장 자료가 가장 많은 7개 유형';

  @override
  String get statusTitle => '효력 상태';

  @override
  String get statusSub => '현재 시행 중인 자료의 비율';

  @override
  String get statusNoData => '아직 상태 데이터가 없습니다.';

  @override
  String labelDocsPct(String label, int count, String pct) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '문서 $countString건',
    );
    return '$label: $_temp0, $pct';
  }

  @override
  String get mostViewed => '가장 많이 본 문서';

  @override
  String get mostViewedSub => '독자가 가장 많은 문서 5건';

  @override
  String get mostViewedEmpty =>
      '아직 기록된 문서 열람이 없습니다. 방문자가 문서 페이지를 열면 집계가 시작됩니다.';

  @override
  String viewedTimes(int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '조회 $countString회',
    );
    return '$_temp0';
  }

  @override
  String get userStudent => '학생';

  @override
  String get userAcademic => '학계 종사자';

  @override
  String get userPractitioner => '법률 실무자';

  @override
  String get userPublic => '일반 시민';

  @override
  String get userOther => '기타';

  @override
  String get aspectAccess => '접근 편의성';

  @override
  String get aspectCompleteness => '정보의 충실성';

  @override
  String get aspectSpeed => '로딩 속도';

  @override
  String get aspectInterface => '화면 디자인';

  @override
  String get aspectRelevance => '검색 정확도';

  @override
  String get errNameRequired => '이름을 입력해 주세요.';

  @override
  String get errEmailFormat => '이메일 형식이 올바르지 않습니다. 예: name@example.com';

  @override
  String get errPickUserType => '사용자 유형을 하나 선택해 주세요.';

  @override
  String get errRateAll => '모든 항목을 평가해 주세요.';

  @override
  String get thankYou => '감사합니다!';

  @override
  String get surveySent => '설문이 제출되었습니다.';

  @override
  String get ok => '확인';

  @override
  String get fieldName => '이름';

  @override
  String get fieldInstitution => '소속 기관';

  @override
  String get fieldUserType => '사용자 유형';

  @override
  String get fieldRating => '서비스 평가';

  @override
  String get fieldSuggestions => '개선 제안';

  @override
  String get fieldFeatures => '원하는 기능';

  @override
  String get fieldContactOk => '연락 수신 동의';

  @override
  String get fieldContact => '연락처(휴대폰/WhatsApp)';

  @override
  String get submitSurvey => '설문 제출';

  @override
  String get surveyHeading => '더 나은 서비스를 위해 도와주세요';

  @override
  String get surveyIntro =>
      '평가는 JDIH 서비스 개선에 도움이 됩니다. \'선택\' 표시 항목은 건너뛰어도 됩니다.';

  @override
  String get surveyAbout => '본인 정보';

  @override
  String get surveyFeedback => '의견';

  @override
  String get optional => '선택';

  @override
  String ratingWord(String n) {
    String _temp0 = intl.Intl.selectLogic(n, {
      '1': '매우 나쁨',
      '2': '나쁨',
      '3': '보통',
      '4': '좋음',
      'other': '매우 좋음',
    });
    return '$_temp0';
  }

  @override
  String surveyProgress(int done, int total) {
    return '필수 항목 $done/$total';
  }

  @override
  String ratingTooltip(String label, int n) {
    return '$label, 5점 중 $n점';
  }
}
