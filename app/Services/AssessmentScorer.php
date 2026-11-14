<?php
namespace App\Services;

/**
 * Transparent scoring rules for the Audit Readiness self-assessment.
 * Each answer maps to points: yes = 2, partial = 1, no/unsure = 0.
 * Domain score = earned / max × 100. Overall = average of domain scores.
 * Bands: 0–39 Early stage, 40–69 Developing, 70–84 Progressing, 85–100 Well prepared.
 * The same definitions are exposed to the frontend so users can see the rules.
 */
class AssessmentScorer
{
    const POINTS = ['yes' => 2, 'partial' => 1, 'no' => 0, 'unsure' => 0];

    const FRAMEWORKS = ['ISO27001' => 'ISO 27001', 'SOC2' => 'SOC 2', 'PCIDSS' => 'PCI DSS', 'DPDPA' => 'DPDPA', 'GDPR' => 'GDPR', 'HIPAA' => 'HIPAA', 'VAPT' => 'VAPT', 'ITAUDIT' => 'IT Audit', 'OTHER' => 'Other'];

    const BANDS = [
        [85, 'Well prepared', 'Your responses indicate mature foundations. An independent readiness review can confirm evidence quality before a formal audit.'],
        [70, 'Progressing', 'Good foundations are in place. Focus on closing remaining gaps and building consistent evidence.'],
        [40, 'Developing', 'Several important controls are partly in place. A structured gap assessment and roadmap would help prioritise effort.'],
        [0, 'Early stage', 'Core security and compliance foundations need attention. Starting with risk assessment and key policies will give the biggest benefit.'],
    ];

    /** Domains → questions. Keys are stable identifiers stored with each submission. */
    public static function domains(): array
    {
        return [
            'policies' => ['Information security policies', [
                'pol_documented' => 'Do you have documented information security policies approved by management?',
                'pol_reviewed'   => 'Are policies reviewed at least annually and communicated to staff?',
            ]],
            'risk' => ['Risk management', [
                'risk_assessment' => 'Do you perform a documented information security risk assessment?',
                'risk_register'   => 'Is there a risk register with owners and treatment plans?',
            ]],
            'access' => ['Access controls', [
                'acc_mfa'     => 'Is multi-factor authentication enforced for remote and privileged access?',
                'acc_reviews' => 'Are user access rights reviewed periodically and removed promptly for leavers?',
            ]],
            'incident' => ['Incident response', [
                'inc_plan'   => 'Do you have a documented incident response plan with defined roles?',
                'inc_tested' => 'Has the incident response plan been tested in the last 12 months?',
            ]],
            'data' => ['Data protection', [
                'data_inventory'  => 'Do you maintain an inventory of sensitive and personal data?',
                'data_encryption' => 'Is sensitive data encrypted at rest and in transit?',
            ]],
            'testing' => ['Security testing', [
                'test_vapt'      => 'Are vulnerability assessments or penetration tests performed at least annually?',
                'test_remediate' => 'Are identified vulnerabilities tracked to closure within defined timelines?',
            ]],
            'documentation' => ['Compliance documentation', [
                'doc_evidence' => 'Do you retain evidence of control operation (logs, approvals, reviews)?',
                'doc_audit'    => 'Have you completed an internal audit or readiness review before?',
            ]],
        ];
    }

    public static function score(array $answers): array
    {
        $domainScores = [];
        $missing = [];
        foreach (self::domains() as $key => $domain) {
            $earned = 0;
            $max = 0;
            foreach ($domain[1] as $qKey => $q) {
                $a = $answers[$qKey] ?? null;
                if (!isset(self::POINTS[$a])) {
                    $missing[] = $qKey;
                    continue;
                }
                $earned += self::POINTS[$a];
                $max += 2;
            }
            $domainScores[$key] = ['label' => $domain[0], 'score' => $max ? (int) round($earned / $max * 100) : 0];
        }
        $overall = (int) round(array_sum(array_column($domainScores, 'score')) / max(1, count($domainScores)));
        $band = self::BANDS[count(self::BANDS) - 1];
        foreach (self::BANDS as $b) {
            if ($overall >= $b[0]) {
                $band = $b;
                break;
            }
        }
        // Recommendations: the weakest domains first.
        $sorted = $domainScores;
        uasort($sorted, function ($a, $b) { return $a['score'] <=> $b['score']; });
        $priorities = [];
        foreach ($sorted as $k => $d) {
            if ($d['score'] < 70) {
                $priorities[] = $d['label'];
            }
        }
        return [
            'missing'    => $missing,
            'score'      => $overall,
            'band'       => $band[1],
            'band_text'  => $band[2],
            'domains'    => $domainScores,
            'priorities' => array_slice($priorities, 0, 3),
        ];
    }
}
